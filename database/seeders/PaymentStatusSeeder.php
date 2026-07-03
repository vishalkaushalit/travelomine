<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentStatus;

class PaymentStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            [
                'name' => 'Payment Pending',
                'slug' => 'pending',
                'color' => '#FFA500',
                'description' => 'Payment is pending - waiting for processing',
                'order' => 1
            ],
            [
                'name' => 'Captured',
                'slug' => 'captured',
                'color' => '#28A745',
                'description' => 'Payment successfully captured and confirmed',
                'order' => 2
            ],
            [
                'name' => 'Failed',
                'slug' => 'failed',
                'color' => '#DC3545',
                'description' => 'Payment failed - requires reprocessing',
                'order' => 3
            ],
            [
                'name' => 'On Hold',
                'slug' => 'hold',
                'color' => '#FFC107',
                'description' => 'Payment is on hold - pending review',
                'order' => 4
            ],
            [
                'name' => 'Refunded',
                'slug' => 'refund',
                'color' => '#6C757D',
                'description' => 'Payment has been refunded',
                'order' => 5
            ],
            [
                'name' => 'Void',
                'slug' => 'void',
                'color' => '#6C757D',
                'description' => 'Payment has been voided/cancelled',
                'order' => 6
            ],
            [
                'name' => 'Declined',
                'slug' => 'declined',
                'color' => '#DC3545',
                'description' => 'Payment was declined by bank',
                'order' => 7
            ]
        ];

        foreach ($statuses as $status) {
            PaymentStatus::create($status);
        }
    }
}