<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Charge;
use App\Models\Organization;

class ChargeSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = Organization::all();

        foreach ($organizations as $org) {
            // Sample 1: Monthly recurring - Draft
            Charge::create([
                'organization_id' => $org->id,
                'title' => 'Monthly Membership Fee',
                'description' => '<p>Regular monthly membership fee for all active members.</p><ul><li>Access to all facilities</li><li>Member events</li><li>Monthly newsletter</li></ul>',
                'amount' => 50.00,
                'type' => 'charge',
                'recurring_frequency' => 'monthly',
                'is_recurring' => true,
                'recurring_months' => 1,
                'status' => 'active',
                'workflow_status' => 'draft',
                'approval_status' => 'pending',
            ]);

            // Sample 2: Quarterly - Submitted
            Charge::create([
                'organization_id' => $org->id,
                'title' => 'Quarterly Maintenance Fee',
                'description' => '<p>Quarterly maintenance and upkeep fee.</p><ul><li>Facility maintenance</li><li>Equipment servicing</li></ul>',
                'amount' => 150.00,
                'type' => 'charge',
                'recurring_frequency' => 'monthly',
                'is_recurring' => true,
                'recurring_months' => 3,
                'status' => 'active',
                'workflow_status' => 'submitted',
                'approval_status' => 'pending',
                'scheduled_at' => now()->addDays(7),
            ]);

            // Sample 3: Semi-annually - Approved
            Charge::create([
                'organization_id' => $org->id,
                'title' => 'Semi-Annual Premium Plan',
                'description' => '<p><strong>Premium membership benefits:</strong></p><ul><li>Priority booking</li><li>Exclusive events access</li><li>VIP parking</li><li>Complimentary guest passes</li></ul>',
                'amount' => 500.00,
                'type' => 'charge',
                'recurring_frequency' => 'semi-annually',
                'is_recurring' => true,
                'recurring_months' => 6,
                'status' => 'active',
                'workflow_status' => 'approved',
                'approval_status' => 'approved',
                'scheduled_at' => now()->addDays(3),
            ]);

            // Sample 4: Annual - Active
            Charge::create([
                'organization_id' => $org->id,
                'title' => 'Annual Platinum Membership',
                'description' => '<p><strong>Platinum tier includes:</strong></p><ul><li>All premium benefits</li><li>Personal concierge service</li><li>Unlimited guest passes</li><li>Annual gala invitation</li><li>Reserved parking spot</li></ul>',
                'amount' => 2000.00,
                'type' => 'charge',
                'recurring_frequency' => 'annually',
                'is_recurring' => true,
                'recurring_months' => 12,
                'status' => 'active',
                'workflow_status' => 'active',
                'approval_status' => 'approved',
            ]);

            // Sample 5: One-time - Active
            Charge::create([
                'organization_id' => $org->id,
                'title' => 'Registration Fee',
                'description' => '<p>One-time registration fee for new members.</p><p>Includes:</p><ul><li>Welcome kit</li><li>Member card</li><li>Orientation session</li></ul>',
                'amount' => 100.00,
                'type' => 'charge',
                'recurring_frequency' => 'one-time',
                'is_recurring' => false,
                'recurring_months' => null,
                'status' => 'active',
                'workflow_status' => 'active',
                'approval_status' => 'approved',
            ]);

            // Sample 6: Bi-monthly - Rejected
            Charge::create([
                'organization_id' => $org->id,
                'title' => 'Bi-Monthly Event Fee',
                'description' => '<p>Special events and activities fee charged every 2 months.</p>',
                'amount' => 80.00,
                'type' => 'charge',
                'recurring_frequency' => 'bi-monthly',
                'is_recurring' => true,
                'recurring_months' => 2,
                'status' => 'active',
                'workflow_status' => 'rejected',
                'approval_status' => 'rejected',
                'reject_reason' => 'The pricing is too high compared to similar organizations. Please revise to RM 50-60 range.',
            ]);
        }
    }
}
