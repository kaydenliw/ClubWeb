<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\Member;
use App\Models\Charge;
use App\Models\Transaction;
use App\Models\Settlement;
use Illuminate\Support\Facades\DB;

class ComprehensiveDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder ensures all pivot tables and relationships are properly populated
     */
    public function run(): void
    {
        $this->command->info('Starting comprehensive data seeding...');

        // Step 1: Sync members to member_organization pivot
        $this->syncMembersToOrganizations();

        // Step 2: Assign charges to members via charge_member pivot
        $this->assignChargesToMembers();

        // Step 3: Create last month transactions for pie chart
        $this->createLastMonthTransactions();

        // Step 4: Create additional settlements if needed
        $this->ensureSettlements();

        $this->command->info('Comprehensive data seeding completed!');
    }

    private function syncMembersToOrganizations()
    {
        $this->command->info('Syncing members to member_organization pivot...');

        $members = Member::whereNotNull('organization_id')->get();
        $synced = 0;

        foreach ($members as $member) {
            // Check if already exists in pivot
            $exists = DB::table('member_organization')
                ->where('member_id', $member->id)
                ->where('organization_id', $member->organization_id)
                ->exists();

            if (!$exists) {
                // Generate membership number
                $membershipNumber = 'MEM-' . str_pad($member->id, 6, '0', STR_PAD_LEFT);

                // Assign random role
                $roles = ['member', 'member', 'member', 'committee', 'treasurer'];
                $role = $roles[array_rand($roles)];

                DB::table('member_organization')->insert([
                    'member_id' => $member->id,
                    'organization_id' => $member->organization_id,
                    'joined_at' => $member->created_at ?? now(),
                    'status' => $member->status ?? 'active',
                    'role' => $role,
                    'membership_number' => $membershipNumber,
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $synced++;
            }
        }

        $this->command->info("✓ Synced {$synced} members to member_organization pivot");
    }

    private function assignChargesToMembers()
    {
        $this->command->info('Assigning charges to members via charge_member pivot...');

        $organizations = Organization::all();
        $assigned = 0;

        foreach ($organizations as $org) {
            $members = Member::where('organization_id', $org->id)->get();
            $charges = Charge::where('organization_id', $org->id)
                ->where('status', 'active')
                ->get();

            if ($charges->isEmpty()) {
                continue;
            }

            foreach ($members as $member) {
                // Assign 1-3 charges to each member
                $chargesToAssign = $charges->random(min(rand(1, 3), $charges->count()));

                foreach ($chargesToAssign as $charge) {
                    // Check if already exists
                    $exists = DB::table('charge_member')
                        ->where('member_id', $member->id)
                        ->where('charge_id', $charge->id)
                        ->exists();

                    if (!$exists) {
                        // Calculate next renewal date based on recurring frequency
                        $nextRenewalDate = $this->calculateNextRenewalDate($charge);

                        // Determine payment status
                        $statuses = ['pending', 'pending', 'paid', 'overdue'];
                        $status = $statuses[array_rand($statuses)];
                        $paidAt = $status === 'paid' ? now()->subDays(rand(1, 30)) : null;

                        DB::table('charge_member')->insert([
                            'member_id' => $member->id,
                            'charge_id' => $charge->id,
                            'amount' => $charge->amount,
                            'status' => $status,
                            'paid_at' => $paidAt,
                            'next_renewal_date' => $nextRenewalDate,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $assigned++;
                    }
                }
            }
        }

        $this->command->info("✓ Assigned {$assigned} charge-member relationships");
    }

    private function calculateNextRenewalDate($charge)
    {
        if (!$charge->is_recurring) {
            return null;
        }

        $now = now();

        switch ($charge->recurring_frequency) {
            case 'monthly':
                return $now->copy()->addMonth();
            case 'bi-monthly':
                return $now->copy()->addMonths(2);
            case 'semi-annually':
                return $now->copy()->addMonths(6);
            case 'annually':
                return $now->copy()->addYear();
            default:
                return null;
        }
    }

    private function createLastMonthTransactions()
    {
        $this->command->info('Creating last month transactions for pie chart...');

        $organizations = Organization::all();
        $created = 0;

        foreach ($organizations as $org) {
            $members = Member::where('organization_id', $org->id)->get();
            $charges = Charge::where('organization_id', $org->id)
                ->where('status', 'active')
                ->get();

            if ($members->isEmpty() || $charges->isEmpty()) {
                continue;
            }

            // Create 10-20 transactions for last month
            $transactionCount = rand(10, 20);

            for ($i = 0; $i < $transactionCount; $i++) {
                $member = $members->random();
                $charge = $charges->random();

                $lastMonth = now()->subMonth();
                $createdAt = $lastMonth->copy()->startOfMonth()->addDays(rand(0, $lastMonth->daysInMonth - 1));

                $platformFee = $charge->amount * 0.05;
                $paymentMethods = ['cash', 'card', 'bank_transfer', 'online'];

                Transaction::create([
                    'organization_id' => $org->id,
                    'member_id' => $member->id,
                    'charge_id' => $charge->id,
                    'transaction_number' => 'TXN' . strtoupper(uniqid()),
                    'amount' => $charge->amount,
                    'platform_fee' => $platformFee,
                    'type' => 'payment',
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'status' => 'completed',
                    'notes' => null,
                    'synced_to_accounting' => rand(0, 1) == 1,
                    'synced_at' => rand(0, 1) == 1 ? $createdAt->copy()->addDays(rand(1, 5)) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                $created++;
            }
        }

        $this->command->info("✓ Created {$created} last month transactions");
    }

    private function ensureSettlements()
    {
        $this->command->info('Ensuring settlements exist for all organizations...');

        $organizations = Organization::all();
        $created = 0;

        foreach ($organizations as $org) {
            // Check if organization has at least one settlement
            $settlementCount = Settlement::where('organization_id', $org->id)->count();

            if ($settlementCount === 0) {
                // Create at least one completed settlement
                $amount = rand(5000, 15000);
                $completedDate = now()->subMonths(rand(1, 2));

                Settlement::create([
                    'organization_id' => $org->id,
                    'settlement_number' => 'STL' . strtoupper(uniqid()),
                    'amount' => $amount,
                    'settlement_date' => $completedDate->toDateString(),
                    'scheduled_date' => $completedDate->toDateString(),
                    'status' => 'completed',
                    'completed_at' => $completedDate,
                    'notes' => 'Settlement transferred to bank account',
                    'approval_status' => 'approved',
                    'approved_at' => $completedDate->copy()->subDays(1),
                    'created_at' => $completedDate->subDays(5),
                    'updated_at' => $completedDate,
                ]);
                $created++;
            }
        }

        $this->command->info("✓ Created {$created} settlements");
    }
}
