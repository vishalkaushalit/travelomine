<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\CallType;
use App\Models\Merchant;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItineraryParserController extends Controller
{
    public function create(): View
    {
        $callTypes      = CallType::where('is_active', true)->orderBy('type_name')->get();
        $serviceTypes   = ServiceType::where('is_active', true)->orderBy('type_name')->get();
        $merchants      = Merchant::where('is_active', true)->orderBy('name')->get();
        $cabinClasses   = $this->getCabinClasses();
        $currencies     = ['USD', 'EUR', 'GBP', 'INR', 'AUD', 'CAD'];
        $serviceProvidedOptions = ['Flight', 'Hotel', 'Package'];

        return view('agent.bookings.createbooking', compact(
            'callTypes', 'serviceTypes', 'merchants', 'currencies',
            'serviceProvidedOptions', 'cabinClasses'
        ));
    }

    private function getCabinClasses(): array
    {
        try {
            $result = \Illuminate\Support\Facades\DB::selectOne(
                "SHOW COLUMNS FROM flight_segments WHERE Field = 'cabin_class'"
            );
            if ($result && isset($result->Type) && preg_match('/^enum\((.*)\)$/', $result->Type, $matches)) {
                return array_map(fn($v) => trim($v, "'"), explode(',', $matches[1]));
            }
        } catch (\Exception $e) {}

        return ['Economy', 'Premium Economy', 'Business', 'First Class'];
    }

    public function decode(Request $request)
    {
        $request->validate(['itinerary' => 'required|string']);

        $decoded = $this->parseAmadeusItinerary($request->input('itinerary'));

        if (!$decoded) {
            return response()->json([
                'success' => false,
                'message' => 'Could not parse the itinerary. Please check the format.',
            ], 422);
        }

        return response()->json(['success' => true, 'data' => $decoded]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MASTER PARSER
    // ─────────────────────────────────────────────────────────────────────────

    private function parseAmadeusItinerary(string $itinerary): ?array
    {
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $itinerary));

        $segments   = [];
        $airlinePnr = null;

        // Extract PNR from any line containing *XXXXX/X*
        foreach ($lines as $line) {
            if (preg_match('/\*([A-Z0-9]{5,8})\/[A-Z]\*/', $line, $m)) {
                $airlinePnr = $m[1];
                break;
            }
        }

        // Parse each line as a potential segment
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;

            $segment = $this->parseSegmentLine($trimmed);
            if ($segment) {
                $segments[] = $segment;
            }
        }

        if (empty($segments)) return null;

        $flightType = $this->determineFlightType($segments);

        return [
            'segments'       => $segments,
            'flight_type'    => $flightType,
            'airline_pnr'    => $airlinePnr,
            'departure_city' => $segments[0]['from_city']                   ?? null,
            'arrival_city'   => $segments[count($segments) - 1]['to_city']  ?? null,
            'departure_date' => $segments[0]['departure_date']               ?? null,
            'airline_name'   => $segments[0]['airline_name']                 ?? null,
            'flight_number'  => $segments[0]['flight_number']                ?? null,
            'cabin_class'    => $segments[0]['cabin_class']                  ?? null,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SINGLE-LINE SEGMENT PARSER
    //
    // Amadeus GDS segment line structure:
    //   <seq>  <airline><flightNo> <cabin> <date> <dow> <from><to> <status>
    //          [<depTime>] [<terminal>] [<stops>] [<arrTime>] [<arrTime2>]
    //          [*PNR*]
    //
    //   Airline formats:  "VS 026"  or  "AF1666"  or  "KL1850"  or  "KL 641"
    //   Time formats:     "815A" "1200A" "1235P" "810P" "835P1020P"
    //   Terminals:        "1"  "2F"  "2E"  (ignored, not stored)
    // ─────────────────────────────────────────────────────────────────────────

    private function parseSegmentLine(string $line): ?array
    {
        /*
         * Capture groups:
         *  1  – airline code  (2 letters)
         *  2  – flight number (digits)
         *  3  – cabin class letter
         *  4  – departure date  (e.g. 12JUN)
         *  5  – day-of-week digit
         *  6  – origin IATA   (3 letters)
         *  7  – destination IATA (3 letters)
         *  8  – status code   (e.g. HK1, SS1)
         *  9  – remainder     (times, terminals, PNR)
         */
        $pattern = '/
            ^\s*                        # optional leading whitespace
            \d+                         # sequence number  (1, 2, 3 …)
            \s+
            ([A-Z]{2})                  # (1) airline code
            \s*                         # optional space between code & number
            (\d+)                       # (2) flight number digits
            \s+
            ([A-Z])                     # (3) cabin class
            \s+
            (\d{2}[A-Z]{3})             # (4) departure date  e.g. 12JUN
            \s+
            (\d)                        # (5) day of week
            \s+
            ([A-Z]{3})                  # (6) from IATA
            ([A-Z]{3})                  # (7) to IATA
            \s+
            ([A-Z]{2}\d+)               # (8) status  e.g. HK1
            (.*)                        # (9) remainder
            $/x';

        if (!preg_match($pattern, $line, $m)) {
            return null;
        }

        [, $airlineCode, $flightNo, $cabinCode, $dateRaw, , $from, $to, , $remainder] = $m;

        [$depTime, $arrTime, $arrDate] = $this->extractTimes($remainder, $dateRaw);

        if (!$depTime && !$arrTime) {
            return null;
        }

        return $this->buildSegment(
            $airlineCode,
            $flightNo,
            $cabinCode,
            $dateRaw,
            $from,
            $to,
            $depTime ?? '',
            $arrTime ?? '',
            $arrDate
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TIME EXTRACTION
    //
    // Remainder examples after status code:
    //   "        4   815A 810P   *1A/E*"       → dep=815A   arr=810P
    //   "  1200A 1   100A 210P   *1A/E*"       → dep=1200A  arr=100A
    //   "   245P 2F  325P 515P   *1A/E*"       → dep=245P   arr=325P
    //   "   755P     835P1020P   *1A/E*"       → dep=755P   arr=835P  (+1 day)
    //   "   730A 2E  830A1040A   *1A/E*"       → dep=730A   arr=830A
    //   "        1   925A1100A   *1A/E*"       → dep=925A   arr=1100A
    //   "           1235P 235P   *1A/E*"       → dep=1235P  arr=235P
    // ─────────────────────────────────────────────────────────────────────────

    private function extractTimes(string $remainder, string $depDateRaw): array
    {
        // Strip PNR tokens e.g. *1A/E*
        $clean = preg_replace('/\*[^*]+\*/', '', $remainder);

        // Collect all time tokens:  3-4 digits + A or P
        preg_match_all('/(\d{3,4}[AP])/', $clean, $times);
        $found = $times[1] ?? [];

        $depTime = $found[0] ?? null;
        $arrTime = $found[1] ?? null;
        $arrDate = null;

        // 3+ times means there is a connecting/next-day arrival shown
        // Take first as departure, last as final arrival on next day
        if (count($found) >= 3) {
            $arrTime = end($found);
            $arrDate = $this->addDaysToAmadeusDate($depDateRaw, 1);
        }

        return [$depTime, $arrTime, $arrDate];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // BUILD SEGMENT ARRAY
    // ─────────────────────────────────────────────────────────────────────────

    private function buildSegment(
        string  $airlineCode,
        string  $flightNo,
        string  $cabinCode,
        string  $dateRaw,
        string  $from,
        string  $to,
        string  $depTimeRaw,
        string  $arrTimeRaw,
        ?string $arrDateRaw = null
    ): array {
        $departureDate = $this->parseAmadeusDate($dateRaw);
        $arrivalDate   = $arrDateRaw
            ? $this->parseAmadeusDate($arrDateRaw)
            : $departureDate;

        return [
            'airline_code'   => strtoupper($airlineCode),
            'airline_name'   => $this->getAirlineName($airlineCode),
            'flight_number'  => ltrim($flightNo, '0') ?: $flightNo,
            'from_city'      => strtoupper($from),
            'to_city'        => strtoupper($to),
            'departure_date' => $departureDate,
            'arrival_date'   => $arrivalDate,
            'departure_time' => $depTimeRaw ? $this->parseAmadeusTime($depTimeRaw) : null,
            'arrival_time'   => $arrTimeRaw ? $this->parseAmadeusTime($arrTimeRaw) : null,
            'cabin_class'    => $this->mapCabinClass($cabinCode),
            'segment_pnr'    => null,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function parseAmadeusDate(string $dateStr): string
    {
        $months = [
            'JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04',
            'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08',
            'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12',
        ];

        $dateStr = strtoupper(trim($dateStr));
        $day     = substr($dateStr, 0, 2);
        $monthAb = substr($dateStr, 2, 3);
        $month   = $months[$monthAb] ?? '01';

        // If the month has already passed this year, assume next year
        $year = (int) date('Y');
        if ((int) $month < (int) date('m')) {
            $year++;
        }

        return sprintf('%04d-%02d-%02d', $year, (int) $month, (int) $day);
    }

    private function addDaysToAmadeusDate(string $dateStr, int $days): string
    {
        $base = \DateTime::createFromFormat('Y-m-d', $this->parseAmadeusDate($dateStr));
        if (!$base) return $this->parseAmadeusDate($dateStr);
        $base->modify("+{$days} day");
        return $base->format('Y-m-d');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TIME HELPER
    //
    // Amadeus time format:  HHMMA  or  HMMA
    //   815A  → 08:15     1200A → 00:00 (midnight)
    //   810P  → 20:10     1235P → 12:35
    //   100A  → 01:00     245P  → 14:45
    // ─────────────────────────────────────────────────────────────────────────

    private function parseAmadeusTime(string $timeStr): ?string
    {
        $timeStr = strtoupper(trim($timeStr));

        if (!preg_match('/^(\d{3,4})([AP])$/', $timeStr, $m)) {
            return null;
        }

        $raw  = $m[1];
        $ampm = $m[2];

        // Split hours and minutes based on length
        if (strlen($raw) === 3) {
            $hour = (int) substr($raw, 0, 1);
            $min  = (int) substr($raw, 1, 2);
        } else {
            $hour = (int) substr($raw, 0, 2);
            $min  = (int) substr($raw, 2, 2);
        }

        // 12-hour → 24-hour conversion
        if ($ampm === 'A') {
            if ($hour === 12) $hour = 0;    // 12xxA = midnight
        } else {
            if ($hour !== 12) $hour += 12;  // 1xxP–11xxP → add 12
        }

        return sprintf('%02d:%02d', $hour, $min);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CABIN CLASS MAP  (GDS booking class → readable name)
    // ─────────────────────────────────────────────────────────────────────────

    private function mapCabinClass(string $code): string
    {
        $map = [
            // First Class
            'F' => 'First Class',
            'A' => 'First Class',
            // Business
            'J' => 'Business',
            'C' => 'Business',
            'D' => 'Business',
            'I' => 'Business',
            'Z' => 'Business',
            // Premium Economy
            'W' => 'Premium Economy',
            'S' => 'Premium Economy',
            // Economy
            'Y' => 'Economy',
            'B' => 'Economy',
            'M' => 'Economy',
            'H' => 'Economy',
            'K' => 'Economy',
            'L' => 'Economy',
            'N' => 'Economy',
            'Q' => 'Economy',
            'T' => 'Economy',
            'V' => 'Economy',
            'X' => 'Economy',
            'E' => 'Economy',
            'G' => 'Economy',
        ];

        return $map[strtoupper($code)] ?? 'Economy';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AIRLINE NAME LOOKUP
    // ─────────────────────────────────────────────────────────────────────────

    private function getAirlineName(string $code): string
    {
        $names = [
            'TK' => 'Turkish Airlines',
            'VS' => 'Virgin Atlantic',
            'AF' => 'Air France',
            'KL' => 'KLM',
            'DL' => 'Delta Air Lines',
            'UA' => 'United Airlines',
            'AA' => 'American Airlines',
            'BA' => 'British Airways',
            'LH' => 'Lufthansa',
            'EK' => 'Emirates',
            'QR' => 'Qatar Airways',
            'EY' => 'Etihad Airways',
            'SQ' => 'Singapore Airlines',
            'CX' => 'Cathay Pacific',
            'AI' => 'Air India',
            'IX' => 'Air India Express',
            '6E' => 'IndiGo',
            'SG' => 'SpiceJet',
            'UK' => 'Vistara',
            'NH' => 'ANA',
            'JL' => 'Japan Airlines',
            'MH' => 'Malaysia Airlines',
            'GA' => 'Garuda Indonesia',
            'TG' => 'Thai Airways',
            'OZ' => 'Asiana Airlines',
            'KE' => 'Korean Air',
            'MS' => 'EgyptAir',
            'SA' => 'South African Airways',
            'ET' => 'Ethiopian Airlines',
            'LX' => 'Swiss International',
            'OS' => 'Austrian Airlines',
            'SK' => 'SAS',
            'AY' => 'Finnair',
            'IB' => 'Iberia',
            'AZ' => 'ITA Airways',
            'TP' => 'TAP Air Portugal',
            'SN' => 'Brussels Airlines',
            'LO' => 'LOT Polish Airlines',
            'OK' => 'Czech Airlines',
            'RO' => 'TAROM',
            'WY' => 'Oman Air',
            'GF' => 'Gulf Air',
            'KU' => 'Kuwait Airways',
            'ME' => 'Middle East Airlines',
            'RJ' => 'Royal Jordanian',
            'PK' => 'Pakistan International',
            'UL' => 'SriLankan Airlines',
        ];

        return $names[strtoupper($code)] ?? $code;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FLIGHT TYPE DETECTION
    // ─────────────────────────────────────────────────────────────────────────

private function determineFlightType(array $segments): string
{
    $count = count($segments);

    if ($count === 1) {
        return 'oneway';
    }

    // Exactly 2 segments: check for pure round-trip (A→B, B→A)
    if ($count === 2) {
        if ($segments[0]['from_city'] === $segments[1]['to_city'] &&
            $segments[0]['to_city']   === $segments[1]['from_city']) {
            return 'roundtrip';
        }
        // 2 segments that are not a pure round-trip (e.g. A→B, B→C) → multicity
        return 'multicity';
    }

    // 3 or more segments → always multicity (even if first origin == last destination)
    return 'multicity';
}

}