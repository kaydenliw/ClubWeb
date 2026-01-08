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
                $this->command->warn("No active charges found for organization: {$org->name}");
                continue;
            }

            foreach ($members as $member) {
                // ENSURE EVERY MEMBER HAS AT LEAST ONE RECURRING CHARGE (no N/A)
                // Get recurring charges (monthly, annually, etc.)
                $recurringCharges = $charges->filter(function($charge) {
                    return $charge->is_recurring && in_array($charge->recurring_frequency, ['monthly', 'bi-monthly', 'semi-annually', 'annually']);
                });

                // If no recurring charges, use any active charge
                if ($recurringCharges->isEmpty()) {
                    $recurringCharges = $charges;
                }

                // Assign 2-3 charges to each member (at least 1 recurring for payment status)
                $numCharges = min(rand(2, 3), $charges->count());

                // Always include at least one recurring charge first
                $chargesToAssign = collect();
                if ($recurringCharges->isNotEmpty()) {
                    $chargesToAssign->push($recurringCharges->random());
                }

                // Add additional random charges
                $remainingCount = $numCharges - $chargesToAssign->count();
                if ($remainingCount > 0 && $charges->count() > 1) {
                    $additionalCharges = $charges->whereNotIn('id', $chargesToAssign->pluck('id'))
                        ->random(min($remainingCount, $charges->count() - 1));
                    $chargesToAssign = $chargesToAssign->merge(
                        is_iterable($additionalCharges) ? $additionalCharges : [$additionalCharges]
                    );
                }

                foreach ($chargesToAssign as $charge) {
                    // Check if already exists
                    $exists = DB::table('charge_member')
                        ->where('member_id', $member->id)
                        ->where('charge_id', $charge->id)
                        ->exists();

                    if (!$exists) {
                        // Calculate next renewal date based on recurring frequency
                        $nextRenewalDate = $this->calculateNextRenewalDate($charge);

                        // More realistic payment status distribution
                        // 60% paid, 25% pending, 10% overdue, 5% due soon
                        $rand = rand(1, 100);
                        if ($rand <= 60) {
                            $status = 'paid';
                            $paidAt = now()->subDays(rand(1, 30));
                        } elseif ($rand <= 85) {
                            $status = 'pending';
                            $paidAt = null;
                        } else {
                            $status = 'overdue';
                            $paidAt = null;
                            // Set renewal date in the past for overdue
                            if ($nextRenewalDate) {
                                $nextRenewalDate = now()->subDays(rand(1, 14));
                            }
                        }

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

        // Add some variation to renewal dates (within +/- 7 days)
        $daysVariation = rand(-7, 7);

        switch ($charge->recurring_frequency) {
            case 'monthly':
                return $now->copy()->addMonth()->addDays($daysVariation);
            case 'bi-monthly':
                return $now->copy()->addMonths(2)->addDays($daysVariation);
            case 'semi-annually':
                return $now->copy()->addMonths(6)->addDays($daysVariation);
            case 'annually':
                return $now->copy()->addYear()->addDays($daysVariation);
            default:
                // For any other frequency, default to 1 month
                return $now->copy()->addMonth();
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
                $this->command->warn("Skipping transactions for {$org->name} - no members or charges");
                continue;
            }

            // Create MORE transactions for a comprehensive pie chart (30-50 per org)
            $transactionCount = rand(30, 50);

            // Ensure each charge type gets some transactions for pie chart variety
            $chargeDistribution = [];
            foreach ($charges as $charge) {
                $chargeDistribution[$charge->id] = rand(3, 10); // Each charge gets 3-10 transactions
            }

            for ($i = 0; $i < $transactionCount; $i++) {
                $member = $members->random();

                // Use weighted distribution to ensure all charges appear in pie chart
                if (!empty($chargeDistribution)) {
                    $chargeId = array_rand($chargeDistribution);
                    $charge = $charges->firstWhere('id', $chargeId);
                    $chargeDistribution[$chargeId]--;
                    if ($chargeDistribution[$chargeId] <= 0) {
                        unset($chargeDistribution[$chargeId]);
                    }
                } else {
                    $charge = $charges->random();
                }

                $lastMonth = now()->subMonth();
                $createdAt = $lastMonth->copy()->startOfMonth()->addDays(rand(0, $lastMonth->daysInMonth - 1));

                $platformFee = $charge->amount * 0.05;
                $paymentMethods = ['online', 'online', 'bank_transfer', 'card', 'cash']; // More online payments

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
                    'synced_to_accounting' => rand(0, 10) > 3, // 70% synced
                    'synced_at' => rand(0, 10) > 3 ? $createdAt->copy()->addDays(rand(1, 5)) : null,
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
        $this->command->info('Ensuring settlements exist with linked transactions...');

        $organizations = Organization::all();
        $created = 0;
        $linked = 0;

        foreach ($organizations as $org) {
            // Get completed transactions without settlement_id
            $unlinkedTransactions = Transaction::where('organization_id', $org->id)
                ->where('status', 'completed')
                ->where('type', 'payment')
                ->whereNull('settlement_id')
                ->get();

            if ($unlinkedTransactions->isEmpty()) {
                $this->command->warn("No unlinked transactions for {$org->name}");
                continue;
            }

            // Group transactions by month for realistic settlements
            $transactionsByMonth = $unlinkedTransactions->groupBy(function($txn) {
                return $txn->created_at->format('Y-m');
            });

            foreach ($transactionsByMonth as $month => $transactions) {
                // Calculate settlement amounts
                $totalAmount = $transactions->sum('amount');
                $totalPlatformFee = $transactions->sum('platform_fee');
                $netAmount = $totalAmount - $totalPlatformFee;

                // Use the latest transaction date in that month
                $latestTxnDate = $transactions->max('created_at');
                $completedDate = $latestTxnDate->copy()->addDays(rand(5, 10));

                // Create settlement
                $settlement = Settlement::create([
                    'organization_id' => $org->id,
                    'settlement_number' => 'STL' . strtoupper(uniqid()),
                    'amount' => $netAmount, // Net amount after platform fee
                    'settlement_date' => $completedDate->toDateString(),
                    'scheduled_date' => $completedDate->toDateString(),
                    'status' => 'completed',
                    'completed_at' => $completedDate,
                    'notes' => 'Settlement for ' . $transactions->count() . ' transactions',
                    'approval_status' => 'approved',
                    'approved_at' => $completedDate->copy()->subDays(1),
                    'created_at' => $completedDate->subDays(5),
                    'updated_at' => $completedDate,
                ]);

                // Link all transactions to this settlement
                foreach ($transactions as $txn) {
                    $txn->update(['settlement_id' => $settlement->id]);
                    $linked++;
                }

                $created++;
            }
        }

        $this->command->info("✓ Created {$created} settlements with {$linked} linked transactions");
    }
}
