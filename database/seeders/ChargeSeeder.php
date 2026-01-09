<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Charge;
use App\Models\Organization;

class ChargeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Cleaning existing charges...');

        // Delete charge-member relationships first (foreign key constraint)
        DB::table('charge_member')->delete();

        // Delete all existing charges to prevent duplicates
        Charge::query()->delete();

        $this->command->info('Creating new charges...');

        $organizations = Organization::all();

        foreach ($organizations as $org) {
            // Sample 1: Basic Membership - Monthly
            Charge::create([
                'organization_id' => $org->id,
                'title' => 'Basic Membership',
                'description' => '<p>Basic membership plan with essential benefits.</p><ul><li>Access to all facilities</li><li>Member events</li><li>Monthly newsletter</li><li>Basic support</li></ul>',
                'amount' => 50.00,
                'type' => 'charge',
                'recurring_frequency' => 'monthly',
                'is_recurring' => true,
                'recurring_months' => 1,
                'status' => 'active',
            ]);

            // Sample 2: Gold Membership - 3 Months
            Charge::create([
                'organization_id' => $org->id,
                'title' => 'Gold Membership',
                'description' => '<p>Gold membership plan with premium benefits.</p><ul><li>All Basic benefits</li><li>Priority booking</li><li>Exclusive events access</li><li>Premium support</li><li>10% discount on merchandise</li></ul>',
                'amount' => 150.00,
                'type' => 'charge',
                'recurring_frequency' => 'monthly',
                'is_recurring' => true,
                'recurring_months' => 3,
                'status' => 'active',
            ]);

            // Sample 3: Platinum Membership - Monthly
            Charge::create([
                'organization_id' => $org->id,
                'title' => 'Platinum Membership',
                'description' => '<p>Platinum membership plan with VIP benefits.</p><ul><li>All Gold benefits</li><li>VIP lounge access</li><li>Personal concierge service</li><li>Unlimited guest passes</li><li>20% discount on all services</li><li>Priority customer support</li></ul>',
                'amount' => 200.00,
                'type' => 'charge',
                'recurring_frequency' => 'monthly',
                'is_recurring' => true,
                'recurring_months' => 1,
                'status' => 'active',
            ]);

            // Sample 4: Registration Fee - One-time
            Charge::create([
                'organization_id' => $org->id,
                'title' => 'Registration Fee',
                'description' => '<p>One-time registration fee for new members.</p><p>Includes:</p><ul><li>Welcome kit</li><li>Member card</li><li>Orientation session</li><li>Official membership certificate</li></ul>',
                'amount' => 100.00,
                'type' => 'charge',
                'recurring_frequency' => 'one-time',
                'is_recurring' => false,
                'recurring_months' => null,
                'status' => 'active',
            ]);
        }
    }
}
