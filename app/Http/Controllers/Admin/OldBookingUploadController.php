<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingCard;
use App\Models\Passenger;
use App\Models\FlightSegment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OldBookingUploadController extends Controller
{
    public function index()
    {
        return view('admin.bookings.upload-old');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $inserted = 0;
        $skipped = 0;
        $failed = 0;
        $errors = [];
        $createdAgents = [];

        try {
            $path = $request->file('file')->getRealPath();
            $handle = fopen($path, 'r');

            if (! $handle) {
                return back()->with('error', 'Unable to read uploaded CSV file.');
            }

            $header = fgetcsv($handle);

            if (! $header) {
                return back()->with('error', 'CSV file is empty or invalid.');
            }

            $header = array_map(fn ($value) => trim($value), $header);

            while (($rowData = fgetcsv($handle)) !== false) {
                try {
                    if (count(array_filter($rowData)) === 0) {
                        continue;
                    }

                    $row = [];
                    foreach ($header as $index => $column) {
                        $row[$column] = $rowData[$index] ?? null;
                    }

                    DB::beginTransaction();

                    $bookingReference = $this->value($row, 'Booking Reference');
                    $email = $this->value($row, 'Email Address');
                    $phone = $this->value($row, 'Calling Number');
                    $bookingDate = $this->parseDate($this->value($row, 'Date'));
                    $airlinePnr = $this->value($row, 'Airline PNR');
                    $gkPnr = $this->value($row, 'GK PNR');

                    $duplicateQuery = Booking::query();

                    if ($bookingReference) {
                        $duplicateQuery->where('booking_reference', $bookingReference);
                    } else {
                        $duplicateQuery->where(function ($q) use ($email, $phone, $bookingDate, $airlinePnr, $gkPnr) {
                            if ($email) {
                                $q->where('customer_email', $email);
                            }
                            if ($phone) {
                                $q->where('customer_phone', $phone);
                            }
                            if ($bookingDate) {
                                $q->whereDate('booking_date', $bookingDate);
                            }
                            if ($airlinePnr) {
                                $q->where('airline_pnr', $airlinePnr);
                            }
                            if ($gkPnr) {
                                $q->where('gk_pnr', $gkPnr);
                            }
                        });
                    }

                    $duplicate = $duplicateQuery->first();

                    if ($duplicate) {
                        DB::rollBack();
                        $skipped++;

                        continue;
                    }

                    [$agent, $wasCreated] = $this->resolveAgentUser($this->value($row, 'Agent Name'));

                    if (! $agent) {
                        throw new \Exception('Agent Name missing in CSV row.');
                    }

                    if ($wasCreated) {
                        $createdAgents[] = $agent->name;
                    }

                    $booking = Booking::create([
                        'user_id' => $agent->id,
                        'agent_custom_id' => $agent->agent_custom_id ?? $agent->id,
                        'dob' => $this->value($row, 'DOB') ?? 'NA',
                        'customer_name' => $this->value($row, 'Any Passenger Name') ?? 'NA',
                        'booking_reference' => $bookingReference,
                        'booking_date' => $bookingDate,
                        'call_type' => $this->value($row, 'Call Type'),
                        'manager' => $this->value($row, 'Manager'),
                        'gk_pnr' => $gkPnr,
                        'airline_pnr' => $airlinePnr,
                        'service_provided' => rtrim((string) $this->value($row, 'Verticals'), ', '),
                        'service_type' => $this->value($row, 'Service Provided'),
                        'booking_portal' => $this->value($row, 'Booking Portal'),
                        'customer_phone' => $phone,
                        'billing_phone' => $this->value($row, 'Billing Phone Number'),
                        'customer_email' => $email,
                        'status' => $this->value($row, 'Booking Status'),
                        'email_auth_taken' => $this->yesNoToBool($this->value($row, 'Email - Auth Taken')),
                        'agency_merchant_name' => $this->value($row, 'Merchant'),
                        'currency' => $this->value($row, 'Currency'),
                        'amount_charged' => $this->toNumber($this->value($row, 'Amount Charged')),
                        'amount_paid_airline' => $this->toNumber($this->value($row, 'Amount paid to airline')),
                        'total_mco' => $this->toNumber($this->value($row, 'Total MCO')),
                        'language' => $this->value($row, 'Language'),
                        'agent_remarks' => $this->value($row, 'Agent remarks if any'),
                        'cabin_class' => $this->value($row, 'Cabin'),
                        'return_date' => $this->parseDate($this->value($row, 'Return Date')),
                        'flight_type' => $this->value($row, 'Trip Details'),
                        'mis_remarks' => $this->value($row, 'Remarks By MIS'),
                        'total_passengers' => 0,
                        'created_at' => $this->parseDateTime($this->value($row, 'Timestamp')) ?? now(),
                        'updated_at' => now(),
                    ]);

                    $passengerName = $this->value($row, 'Any Passenger Name');
                    if ($passengerName) {
                        [$firstName, $lastName] = $this->splitName($passengerName);

                        Passenger::create([
                            'booking_id' => $booking->id,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'dob' => $this->parseDate($this->value($row, 'DOB')) ?? '1990-01-01',

                        ]);

                        $booking->update(['total_passengers' => 1]);
                    }

                    $cardHolder = $this->value($row, 'Card Holder Name');
                    $cardLastFour = $this->value($row, 'Card Last 4 digit');
                    if ($cardHolder || $cardLastFour) {
                        BookingCard::create([
                            'booking_id' => $booking->id,
                            'card_holder_name' => $cardHolder,
                            'card_last_four' => $cardLastFour,
                            'card_order' => 1,
                        ]);
                    }

                    $sector = $this->value($row, 'Sector');
                    $travelDate = $this->value($row, 'Travel Date');
                    $airline = $this->value($row, 'Airline');

                    if ($sector || $travelDate || $airline || $airlinePnr || $gkPnr) {
                        [$from, $to] = $this->splitSector($sector);

                        FlightSegment::create([
                            'booking_id' => $booking->id,
                            'from_city' => $from,
                            'to_city' => $to,
                            'from_airport' => $from,
                            'to_airport' => $to,
                            'departure_date' => $this->parseDateFromList($travelDate),
                            'airline_code' => $this->firstFromList($airline),
                            'airline_pnr' => $airlinePnr,
                            'gk_pnr' => $gkPnr,
                        ]);
                    }

                    DB::commit();
                    $inserted++;
                } catch (\Throwable $e) {
                    DB::rollBack();
                    $failed++;
                    $errors[] = $e->getMessage();
                }
            }

            fclose($handle);

            $createdAgents = array_values(array_unique($createdAgents));

            return back()
                ->with('success', "Import completed. Inserted: {$inserted}, Skipped: {$skipped}, Failed: {$failed}")
                ->with('import_errors', array_slice($errors, 0, 20))
                ->with('created_agents', $createdAgents);
        } catch (\Throwable $e) {
            return back()->with('error', 'Import failed: '.$e->getMessage());
        }
    }

    private function resolveAgentUser(?string $agentName): array
    {
        $agentName = trim((string) $agentName);

        if ($agentName === '') {
            return [null, false];
        }

        $agent = User::where('role', 'agent')
            ->whereRaw('LOWER(name) = ?', [strtolower($agentName)])
            ->first();

        if ($agent) {
            return [$agent, false];
        }

        $tempPassword = Str::random(12);

        $agent = User::create([
            'name' => $agentName,
            'email' => 'imported.agent.'.Str::slug($agentName).'.'.time().rand(100, 999).'@temp.local',
            'password' => Hash::make($tempPassword),
            'role' => 'agent',
            'phone' => null,
            'status' => 0,
            'agent_custom_id' => 'AGENT-'.strtoupper(Str::random(6)),
        ]);

        return [$agent, true];
    }

    private function value(array $row, string $key): ?string
    {
        $value = $row[$key] ?? null;

        return $value !== null ? trim((string) $value) : null;
    }

    private function yesNoToBool(?string $value): bool
    {
        return strtolower((string) $value) === 'yes';
    }

    private function toNumber(?string $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) str_replace([',', '$'], '', $value);
    }

    private function parseDate(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function parseDateTime(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function splitName(?string $name): array
    {
        $name = trim((string) $name);

        if ($name === '') {
            return [null, null];
        }

        $parts = preg_split('/\s+/', $name);
        $firstName = array_shift($parts);
        $lastName = count($parts) ? implode(' ', $parts) : null;

        return [$firstName, $lastName];
    }

    private function splitSector(?string $sector): array
    {
        if (! $sector) {
            return [null, null];
        }

        $firstSector = explode(',', $sector)[0];
        $parts = preg_split('/\s*-\s*/', trim($firstSector));

        return [
            $parts[0] ?? null,
            $parts[1] ?? null,
        ];
    }

    private function parseDateFromList(?string $dates): ?string
    {
        if (! $dates) {
            return null;
        }

        $firstDate = trim(explode(',', $dates)[0]);

        return $this->parseDate($firstDate);
    }

    private function firstFromList(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return trim(explode(',', $value)[0]);
    }
}
