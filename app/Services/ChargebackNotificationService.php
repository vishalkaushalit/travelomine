<?php

namespace App\Services;

use App\Mail\ChargebackStatusUpdated;
use App\Models\Booking;
use App\Models\ChargebackRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ChargebackNotificationService
{
    /**
     * Send notifications to all relevant parties about a chargeback status update
     */
    public function sendStatusUpdateNotification(Booking $booking, ChargebackRecord $chargebackRecord): void
    {
        try {
            // 1. Send to Admin(s)
            $this->sendToAdmins($booking, $chargebackRecord);
            
            // 2. Send to MIS Team
            $this->sendToMISTeam($booking, $chargebackRecord);
            
            // 3. Send to MIS Manager
            $this->sendToMISManager($booking, $chargebackRecord);
            
            // 4. Send to Agent who created the booking
            $this->sendToAgent($booking, $chargebackRecord);
            
            Log::info('Chargeback status update notifications sent successfully', [
                'booking_id' => $booking->id,
                'status' => $chargebackRecord->status,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send chargeback notifications', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function getAdminEmails(): array
{
    $emailsString = env('ADMIN_NOTIFICATION_EMAILS', '');
    return $emailsString ? explode(',', $emailsString) : [];
}

private function getMISTeamEmails(): array
{
    $emailsString = env('MIS_TEAM_EMAILS', '');
    return $emailsString ? explode(',', $emailsString) : [];
}

private function getMISManagerEmails(): array
{
    $emailsString = env('MIS_MANAGER_EMAILS', '');
    return $emailsString ? explode(',', $emailsString) : [];
}

    /**
     * Send notification to admin users
     */
    private function sendToAdmins(Booking $booking, ChargebackRecord $chargebackRecord): void
    {
        // Get admin emails from config or database
        $adminEmails = $this->getAdminEmails();
        
        foreach ($adminEmails as $email) {
            try {
                Mail::to($email)->queue(
                    new ChargebackStatusUpdated($booking, $chargebackRecord, 'admin')
                );
            } catch (\Exception $e) {
                Log::error("Failed to send admin notification to {$email}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send notification to MIS team members
     */
    private function sendToMISTeam(Booking $booking, ChargebackRecord $chargebackRecord): void
    {
        // Get MIS team emails from database or config
        $misEmails = $this->getMISTeamEmails();
        
        foreach ($misEmails as $email) {
            try {
                Mail::to($email)->queue(
                    new ChargebackStatusUpdated($booking, $chargebackRecord, 'mis')
                );
            } catch (\Exception $e) {
                Log::error("Failed to send MIS notification to {$email}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send notification to MIS Manager
     */
    private function sendToMISManager(Booking $booking, ChargebackRecord $chargebackRecord): void
    {
        $misManagerEmails = $this->getMISManagerEmails();
        
        foreach ($misManagerEmails as $email) {
            try {
                Mail::to($email)->queue(
                    new ChargebackStatusUpdated($booking, $chargebackRecord, 'mis_manager')
                );
            } catch (\Exception $e) {
                Log::error("Failed to send MIS Manager notification to {$email}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send notification to the agent who created the booking
     */
    private function sendToAgent(Booking $booking, ChargebackRecord $chargebackRecord): void
    {
        if ($booking->user && $booking->user->email) {
            try {
                Mail::to($booking->user->email)->queue(
                    new ChargebackStatusUpdated($booking, $chargebackRecord, 'agent')
                );
            } catch (\Exception $e) {
                Log::error("Failed to send agent notification", [
                    'agent_email' => $booking->user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::warning("No agent email found for booking #{$booking->id}");
        }
    }

    /**
     * Get admin email addresses
     * You can modify this to fetch from database or config
     */


}