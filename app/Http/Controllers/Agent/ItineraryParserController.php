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
        $callTypes = CallType::where('is_active', true)->orderBy('type_name')->get();
        $serviceTypes = ServiceType::where('is_active', true)->orderBy('type_name')->get();
        $merchants = Merchant::where('is_active', true)->orderBy('name')->get();
        $cabinClasses = $this->getCabinClasses();
        $currencies = ['USD', 'EUR', 'GBP', 'INR', 'AUD', 'CAD'];
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
        } catch (\Exception $e) {
        }

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
        $itinerary = $this->normalizeInput($itinerary);
        $lines = explode("\n", $itinerary);

        $segments = [];
        $airlinePnr = null;

        // Extract PNR from any line containing *XXXXX/X*
        foreach ($lines as $line) {
            if (preg_match('/\*([A-Za-z0-9]{5,8})\/?[A-Za-z0-9]?\*/', $line, $m)) {
                $airlinePnr = strtoupper($m[1]);
                break;
            }
        }

        // Parse each line as a potential segment
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            $segment = $this->parseSegmentLine($trimmed);
            if ($segment) {
                $segments[] = $segment;
            }
        }

        if (empty($segments)) {
            return null;
        }

        $flightType = $this->determineFlightType($segments);

        return [
            'segments'       => $segments,
            'flight_type'    => $flightType,
            'airline_pnr'    => $airlinePnr,
            'departure_city' => $segments[0]['from_city'] ?? null,
            'arrival_city'   => $segments[count($segments) - 1]['to_city'] ?? null,
            'departure_date' => $segments[0]['departure_date'] ?? null,
            'airline_name'   => $segments[0]['airline_name'] ?? null,
            'flight_number'  => $segments[0]['flight_number'] ?? null,
            'cabin_class'    => $segments[0]['cabin_class'] ?? null,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SINGLE-LINE SEGMENT PARSER
    // ─────────────────────────────────────────────────────────────────────────

    private function parseSegmentLine(string $line): ?array
    {
        $pattern = '/
            ^\s*
            \d+                         # sequence number
            \s+
            ([A-Z0-9]{2,3})             # airline code (2-3 chars)
            \s*
            (\d+)                       # flight number
            \s+
            ([A-Z])                     # cabin class
            \s+
            (\d{2}[A-Z]{3})             # departure date (e.g. 12JUN)
            \s+
            (\d)                        # day of week
            \s+
            ([A-Z]{3})                  # origin IATA
            \s*
            ([A-Z]{3})                  # destination IATA
            \s+
            ([A-Z]{2}\d+)               # status code (e.g. HK1, GK1)
            (.*)                        # remainder
        /x';

        if (!preg_match($pattern, $line, $m)) {
            return null;
        }

        [, $airlineCode, $flightNo, $cabinCode, $dateRaw, , $from, $to, , $remainder] = $m;

        [$depTime, $arrTime, $arrDate] = $this->extractTimes($remainder, $dateRaw);

        if (!$depTime && !$arrTime) {
            return null;
        }

        $segment = $this->buildSegment(
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

        return $this->validateSegment($segment);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TIME EXTRACTION
    // ─────────────────────────────────────────────────────────────────────────

    private function extractTimes(string $remainder, string $depDateRaw): array
    {
        // Strip PNR tokens (e.g. *1A/E*)
        $clean = preg_replace('/\*[^*]+\*/', '', $remainder);

        // Check for next-day indicator (+1, +2, etc.)
        $nextDay = preg_match('/\+(\d+)/', $clean, $dayMatch);
        $daysToAdd = $nextDay ? (int)$dayMatch[1] : 0;
        $clean = preg_replace('/\+(\d+)/', '', $clean);

        // Remove terminal identifiers (single letter/digit before times)
        $clean = preg_replace('/\b([A-Z0-9])\b(?=\s+\d{3,4}[AP])/', '', $clean);

        // Remove trailing numbers (like "123" in examples)
        $clean = preg_replace('/\s+\d+\s*$/', '', $clean);

        // Collect all time tokens (3-4 digits + A or P)
        preg_match_all('/(\d{3,4}[AP])/', $clean, $times);
        $found = $times[1] ?? [];

        // Validate found tokens
        $found = array_values(array_filter($found, function ($time) {
            return preg_match('/^(?:\d{3,4})[AP]$/', $time);
        }));

        $depTime = $found[0] ?? null;
        $arrTime = $found[1] ?? null;
        $arrDate = null;

        if (empty($found)) {
            return [null, null, null];
        }

        // Handle explicit next-day offset
        if ($daysToAdd > 0 && $arrTime) {
            $arrDate = $this->addDaysToAmadeusDate($depDateRaw, $daysToAdd);
        }
        // Multiple times → last one is final arrival (often next day)
        elseif (count($found) >= 3) {
            $arrTime = end($found);
            $arrDate = $this->addDaysToAmadeusDate($depDateRaw, 1);
        }
        // Smart detection: if arrival time < departure time, assume next day
        elseif ($depTime && $arrTime) {
            $depMinutes = $this->timeToMinutes($depTime);
            $arrMinutes = $this->timeToMinutes($arrTime);
            if ($arrMinutes < $depMinutes) {
                $arrDate = $this->addDaysToAmadeusDate($depDateRaw, 1);
            }
        }

        return [$depTime, $arrTime, $arrDate];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // BUILD SEGMENT ARRAY
    // ─────────────────────────────────────────────────────────────────────────

    private function buildSegment(
        string $airlineCode,
        string $flightNo,
        string $cabinCode,
        string $dateRaw,
        string $from,
        string $to,
        string $depTimeRaw,
        string $arrTimeRaw,
        ?string $arrDateRaw = null
    ): array {
        $departureDate = $this->parseAmadeusDate($dateRaw);
        $arrivalDate = $arrDateRaw
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
        $day = substr($dateStr, 0, 2);
        $monthAb = substr($dateStr, 2, 3);
        $month = $months[$monthAb] ?? '01';

        $year = (int) date('Y');
        // Start with current year
        $candidate = \DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $year, (int)$month, (int)$day));
        // If the date is already in the past, assume next year
        if ($candidate && $candidate < new \DateTime('today')) {
            $year++;
        }

        return sprintf('%04d-%02d-%02d', $year, (int)$month, (int)$day);
    }

    private function addDaysToAmadeusDate(string $dateStr, int $days): string
    {
        $base = \DateTime::createFromFormat('Y-m-d', $this->parseAmadeusDate($dateStr));
        if (!$base) {
            return $this->parseAmadeusDate($dateStr);
        }
        $base->modify("+{$days} day");
        return $base->format('Y-m-d');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TIME HELPER
    // ─────────────────────────────────────────────────────────────────────────

    private function parseAmadeusTime(string $timeStr): ?string
    {
        $timeStr = strtoupper(trim($timeStr));

        if (!preg_match('/^(\d{3,4})([AP])$/', $timeStr, $m)) {
            return null;
        }

        $raw = $m[1];
        $ampm = $m[2];

        if (strlen($raw) === 3) {
            $hour = (int) substr($raw, 0, 1);
            $min  = (int) substr($raw, 1, 2);
        } else {
            $hour = (int) substr($raw, 0, 2);
            $min  = (int) substr($raw, 2, 2);
        }

        if ($ampm === 'A') {
            if ($hour === 12) $hour = 0;
        } else {
            if ($hour !== 12) $hour += 12;
        }

        return sprintf('%02d:%02d', $hour, $min);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CABIN CLASS MAP
    // ─────────────────────────────────────────────────────────────────────────

    private function mapCabinClass(string $code): string
    {
        $map = [
            'F' => 'First Class',
            'A' => 'First Class',
            'J' => 'Business',
            'C' => 'Business',
            'D' => 'Business',
            'I' => 'Business',
            'Z' => 'Business',
            'W' => 'Premium Economy',
            'S' => 'Premium Economy',
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
        // (Your existing airline names array – kept exactly as before, no duplicates)
        $names = [
            'TK' => 'Turkish Airlines',
            'VS' => 'Virgin Atlantic',
            'AF' => 'Air France',
            'KL' => 'KLM Royal Dutch Airlines',
            'DL' => 'Delta Air Lines',
            'UA' => 'United Airlines',
            'AA' => 'American Airlines',
            'BA' => 'British Airways',
            'LH' => 'Lufthansa',
            'U2' => 'easyJet',
            'EK' => 'Emirates',
            'QR' => 'Qatar Airways',
            'EY' => 'Etihad Airways',
            'SQ' => 'Singapore Airlines',
            'CX' => 'Cathay Pacific',
            'AI' => 'Air India',
            'IX' => 'Air India Express',
            'AM' => 'Aeroméxico',
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
            'F9' => 'Frontier Airlines',
            'WN' => 'Southwest Airlines',
            'AS' => 'Alaska Airlines',
            'B6' => 'JetBlue Airways',
            'NK' => 'Spirit Airlines',
            'HA' => 'Hawaiian Airlines',
            'G4' => 'Allegiant Air',
            'SY' => 'Sun Country Airlines',
            'F8' => 'Norwegian Air Shuttle',
            'VY' => 'Vueling Airlines',
            'S7' => 'S7 Airlines',
            'SU' => 'Aeroflot',
            '9k' => 'Cape Air',
            'AC' => 'Air Canada',
            'NZ' => 'Air New Zealand',
            'QF' => 'Qantas',
            'VA' => 'Virgin Australia',
            'HU' => 'Hainan Airlines',
            'CZ' => 'China Southern Airlines',
            'MU' => 'China Eastern Airlines',
            'CA' => 'Air China',
            'BR' => 'EVA Air',
            'CI' => 'China Airlines',
            'PR' => 'Philippine Airlines',
            'VN' => 'Vietnam Airlines',
            'BI' => 'Royal Brunei Airlines',
            'PG' => 'Bangkok Airways',
            'FD' => 'Thai AirAsia',
            'AK' => 'AirAsia',
            'QZ' => 'Indonesia AirAsia',
            'D7' => 'AirAsia X',
            'TR' => 'Scoot',
            'JQ' => 'Jetstar Airways',
            '3K' => 'Jetstar Asia',
            'GK' => 'Jetstar Japan',
            'BL' => 'Jetstar Pacific',
            'AV' => 'Avianca',
            'CM' => 'Copa Airlines',
            'AR' => 'Aerolíneas Argentinas',
            'LA' => 'LATAM Airlines',
            'JJ' => 'LATAM Brasil',
            'LP' => 'LATAM Perú',
            'XL' => 'LATAM Ecuador',
            '4C' => 'LATAM Colombia',
            'UC' => 'LATAM Cargo',
            'P5' => 'LATAM Express',
            'EW' => 'Eurowings',
            'FR' => 'Ryanair',
            'W6' => 'Wizz Air',
            'DY' => 'Norwegian Air Shuttle',
            'DE' => 'Condor',
            'X3' => 'TUI fly',
            'BY' => 'TUI Airways',
            '4U' => 'Germanwings',
            'HG' => 'Niki',
            '2L' => 'Helvetic Airways',
            '4X' => 'Air Corsica',
            'XS' => 'SITA',
            'Y4' => 'Volaris',
            'VB' => 'Viva Aerobus',
            'YQ' => 'JetSmart',
            'H2' => 'Sky Airline',
            'PZ' => 'TAM Mercosur',
            'ZP' => 'Amaszonas',
            'OB' => 'Boliviana de Aviación',
            'AD' => 'Azul Brazilian Airlines',
            'G3' => 'Gol Transportes Aéreos',
            '2Z' => 'Passaredo',
            'TT' => 'TAP Express',
            'NI' => 'Portugália',
            'CS' => 'SATA Azores',
            'S4' => 'SATA Internacional',
            'AT' => 'Royal Air Maroc',
            'TU' => 'Tunisair',
            'AH' => 'Air Algérie',
            'KQ' => 'Kenya Airways',
            'TC' => 'Air Tanzania',
            'UR' => 'Uganda Airlines',
            'WB' => 'RwandAir',
            'HM' => 'Air Seychelles',
            'MD' => 'Air Madagascar',
            'MK' => 'Air Mauritius',
            'XQ' => 'SunExpress',
            'PC' => 'Pegasus Airlines',
            'VF' => 'Valuair',
            '8B' => 'TransNusa',
            'IN' => 'NAM Air',
            'SJ' => 'Sriwijaya Air',
            'JT' => 'Lion Air',
            'IW' => 'Wings Air',
            'QG' => 'Citilink',
            'IP' => 'Pelita Air',
            'IU' => 'Super Air Jet',
            'OD' => 'Batik Air',
            'ID' => 'Batik Air Malaysia',
            'AW' => 'Africa World Airlines',
            'OP' => 'Chalk\'s International Airlines',
            'D8' => 'Norwegian Air Sweden',
            'BT' => 'Air Baltic',
            'KF' => 'Air Serbia',
            'JU' => 'Air Serbia',
            'HV' => 'Transavia',
            'TO' => 'Transavia France',
            'V7' => 'Volotea',
            'E8' => 'Eurowings Europe',
            '4Y' => 'Eurowings Discover',
            'DK' => 'Sunclass Airlines',
            'D2' => 'Severstal Air Company',
            'EO' => 'Hahn Air',
            'HX' => 'Hong Kong Airlines',
            'UO' => 'Hong Kong Express',
            '5J' => 'Cebu Pacific',
            'PQ' => 'Philippines AirAsia',
            'Z2' => 'Philippines AirAsia',
            'KZ' => 'Nippon Cargo Airlines',
            'PO' => 'Polar Air Cargo',
            'FX' => 'FedEx Express',
            '5X' => 'UPS Airlines',
            'RU' => 'AirBridgeCargo',
            'CV' => 'Cargolux',
            'CK' => 'China Cargo Airlines',
            'FM' => 'Shanghai Airlines',
            'KN' => 'China United Airlines',
            'GS' => 'Tianjin Airlines',
            'JD' => 'Capital Airlines',
            '8L' => 'Lucky Air',
            'PN' => 'West Air',
            'GT' => 'Air Guilin',
            'AQ' => '9 Air',
            'EU' => 'Chengdu Airlines',
            'G5' => 'China Express Airlines',
            'JR' => 'Joy Air',
            'KY' => 'Kunming Airlines',
            'NS' => 'Hebei Airlines',
            'TV' => 'Tibet Airlines',
            'FU' => 'Fuzhou Airlines',
            'Y8' => 'Yangtze River Express',
            'DZ' => 'Donghai Airlines',
            'QW' => 'Qingdao Airlines',
            'HO' => 'Juneyao Air',
            'ZH' => 'Shenzhen Airlines',
            'SC' => 'Shandong Airlines',
            'MF' => 'Xiamen Airlines',
            '3U' => 'Sichuan Airlines',
            'BK' => 'Okay Airways',
            'DR' => 'Ruili Airlines',
            'CN' => 'Grand China Air',
            '9H' => 'Hainan Airlines',
            'AE' => 'Mandarin Airlines',
            'B7' => 'Uni Air',
            'IT' => 'Tigerair Taiwan',
            'JX' => 'Starlux Airlines',
            'SX' => 'SkyUp Airlines',
            'PS' => 'Ukraine International Airlines',
            'U8' => 'Belavia',
            'B2' => 'Belavia',
            'OM' => 'MIAT Mongolian Airlines',
            'MR' => 'Mongolian Airlines',
            '8I' => 'Myanmar Airways',
            'UB' => 'Myanmar National Airlines',
            'W9' => 'Air KBZ',
            'K7' => 'Golden Myanmar Airlines',
            'ST' => 'Air Mandalay',
            '9Q' => 'Cambodia Angkor Air',
            'K6' => 'Cambodia Airways',
            'ZA' => 'Sky Angkor Airlines',
            'KT' => 'Lanmei Airlines',
            'LQ' => 'Lao Airlines',
            'QV' => 'Lao Central Airlines',
            'K3' => 'KrasAvia',
            'KV' => 'KrasAvia',
            '5N' => 'Nordavia',
            'N4' => 'Nordwind Airlines',
            'Y7' => 'NordStar Airlines',
            'WZ' => 'Red Wings Airlines',
            'RL' => 'Royal Flight',
            'ZF' => 'Azur Air',
            'IK' => 'Ikar',
            'NG' => 'Aeroflot-Nord',
            'FV' => 'Rossiya Airlines',
            'SD' => 'Sirin Airlines',
            'FS' => 'UTair Express',
            'UT' => 'UTair Aviation',
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

        if ($count === 2) {
            if ($segments[0]['from_city'] === $segments[1]['to_city'] &&
                $segments[0]['to_city'] === $segments[1]['from_city']) {
                return 'roundtrip';
            }
            return 'multicity';
        }

        return 'multicity';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VALIDATION & HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function validateSegment(array $segment): ?array
    {
        if (!preg_match('/^[A-Z]{3}$/', $segment['from_city']) ||
            !preg_match('/^[A-Z]{3}$/', $segment['to_city'])) {
            return null;
        }

        try {
            new \DateTime($segment['departure_date']);
            new \DateTime($segment['arrival_date']);
        } catch (\Exception $e) {
            return null;
        }

        if ($segment['arrival_date'] === $segment['departure_date'] &&
            $segment['departure_time'] && $segment['arrival_time']) {
            if (strtotime($segment['arrival_time']) <= strtotime($segment['departure_time'])) {
                return null;
            }
        }

        return $segment;
    }

    private function normalizeInput(string $itinerary): string
    {
        $itinerary = str_replace(["\r\n", "\r"], "\n", $itinerary);
        $itinerary = preg_replace('/[ \t]+/', ' ', $itinerary);
        return $itinerary;
    }

    private function timeToMinutes(string $time): int
    {
        $parsed = $this->parseAmadeusTime($time);
        if (!$parsed) {
            return 0;
        }
        [$hours, $minutes] = explode(':', $parsed);
        return (int)$hours * 60 + (int)$minutes;
    }
                                                }