<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\Member;
use App\Models\Charge;
use App\Models\Transaction;
use App\Models\Settlement;
use App\Models\ContactTicket;
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

        // Step 2: Create multi-organization members (realistic scenario)
        $this->createMultiOrganizationMembers();

        // Step 3: Assign charges to members via charge_member pivot
        $this->assignChargesToMembers();

        // Step 4: Create last month transactions for pie chart
        $this->createLastMonthTransactions();

        // Step 5: Create support tickets for multiple organizations
        $this->createSupportTickets();

        // Step 6: Create additional settlements if needed
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

    private function createMultiOrganizationMembers()
    {
        $this->command->info('Creating multi-organization members (realistic scenario)...');

        $organizations = Organization::all();

        if ($organizations->count() < 2) {
            $this->command->warn('Need at least 2 organizations for multi-membership scenario');
            return;
        }

        // Get some existing members to add to multiple organizations
        $members = Member::inRandomOrder()->limit(10)->get();
        $added = 0;

        foreach ($members as $member) {
            // Each member joins 1-2 additional organizations (20-30% chance per org)
            $additionalOrgs = $organizations->where('id', '!=', $member->organization_id)
                ->random(min(rand(1, 2), $organizations->count() - 1));

            foreach ($additionalOrgs as $org) {
                // Check if already exists
                $exists = DB::table('member_organization')
                    ->where('member_id', $member->id)
                    ->where('organization_id', $org->id)
                    ->exists();

                if (!$exists) {
                    $membershipNumber = 'MEM-' . strtoupper(substr($org->name, 0, 3)) . '-' . str_pad($member->id, 4, '0', STR_PAD_LEFT);
                    $roles = ['member', 'member', 'member', 'committee'];

                    $pivotId = DB::table('member_organization')->insertGetId([
                        'member_id' => $member->id,
                        'organization_id' => $org->id,
                        'joined_at' => now()->subMonths(rand(1, 12)),
                        'status' => 'active',
                        'role' => $roles[array_rand($roles)],
                        'membership_number' => $membershipNumber,
                        'notes' => 'Multi-organization member',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Create organization-specific details based on type
                    if ($org->organizationType) {
                        $this->createOrganizationDetails($pivotId, $org->organizationType->slug);
                    }

                    $added++;
                }
            }
        }

        $this->command->info("✓ Added {$added} multi-organization memberships");
    }

    private function assignChargesToMembers()
    {
        $this->command->info('Cleaning existing charge-member relationships...');

        // Clean all existing charge-member relationships
        DB::table('charge_member')->delete();

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
                // Assign subscription plans to members (only recurring charges)
                // Get Basic, Gold, and Platinum membership plans
                $basicPlan = $charges->filter(function($charge) {
                    return $charge->title === 'Basic Membership' && $charge->is_recurring;
                });

                $goldPlan = $charges->filter(function($charge) {
                    return $charge->title === 'Gold Membership' && $charge->is_recurring;
                });

                $platinumPlan = $charges->filter(function($charge) {
                    return $charge->title === 'Platinum Membership' && $charge->is_recurring;
                });

                // Assign 1 subscription plan to each member
                $chargesToAssign = collect();

                // Randomly assign one of the three plans
                $availablePlans = [];
                if ($basicPlan->isNotEmpty()) $availablePlans[] = $basicPlan->first();
                if ($goldPlan->isNotEmpty()) $availablePlans[] = $goldPlan->first();
                if ($platinumPlan->isNotEmpty()) $availablePlans[] = $platinumPlan->first();

                if (!empty($availablePlans)) {
                    // Assign 1 random plan
                    $chargesToAssign->push($availablePlans[array_rand($availablePlans)]);
                }

                // If no subscription plans found, fall back to any recurring charge
                if ($chargesToAssign->isEmpty()) {
                    $recurringCharges = $charges->where('is_recurring', true);
                    if ($recurringCharges->isNotEmpty()) {
                        $chargesToAssign->push($recurringCharges->random());
                    }
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

    private function createSupportTickets()
    {
        $this->command->info('Creating support tickets for multiple organizations...');

        $organizations = Organization::all();
        $created = 0;

        $subjects = [
            'Payment Issue - Unable to process payment',
            'Membership Card Request',
            'Update Contact Information',
            'Event Registration Question',
            'Billing Inquiry - Duplicate Charge',
            'Account Access Problem',
            'Refund Request',
            'Change Membership Plan',
            'Technical Support Needed',
            'General Inquiry'
        ];

        $messages = [
            'I am having trouble accessing my account. Can you please help?',
            'I would like to request a refund for the recent charge.',
            'Could you please update my contact information in the system?',
            'I need assistance with registering for the upcoming event.',
            'I noticed a duplicate charge on my account. Please investigate.',
            'My payment was declined but I have sufficient funds. What should I do?',
            'I would like to upgrade my membership plan. What are the options?',
            'Can I get a physical membership card sent to my address?',
            'I am experiencing technical issues with the member portal.',
            'I have a general question about the membership benefits.'
        ];

        foreach ($organizations as $org) {
            // Get members who belong to this organization
            $memberIds = DB::table('member_organization')
                ->where('organization_id', $org->id)
                ->pluck('member_id');

            if ($memberIds->isEmpty()) {
                continue;
            }

            // Create 5-15 tickets per organization
            $ticketCount = rand(5, 15);

            for ($i = 0; $i < $ticketCount; $i++) {
                $memberId = $memberIds->random();
                $createdAt = now()->subDays(rand(1, 60));

                $statuses = ['open', 'open', 'replied', 'replied', 'closed'];
                $status = $statuses[array_rand($statuses)];

                $priorities = ['low', 'low', 'medium', 'medium', 'high'];
                $priority = $priorities[array_rand($priorities)];

                $ticket = ContactTicket::create([
                    'organization_id' => $org->id,
                    'member_id' => $memberId,
                    'ticket_number' => 'TKT-' . strtoupper(uniqid()),
                    'subject' => $subjects[array_rand($subjects)],
                    'message' => $messages[array_rand($messages)],
                    'status' => $status,
                    'priority' => $priority,
                    'category' => ['billing', 'technical', 'general', 'membership'][array_rand(['billing', 'technical', 'general', 'membership'])],
                    'reply' => $status !== 'open' ? 'Thank you for contacting us. We have reviewed your request and will assist you shortly.' : null,
                    'replied_at' => $status !== 'open' ? $createdAt->copy()->addHours(rand(2, 48)) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $created++;
            }
        }

        $this->command->info("✓ Created {$created} support tickets across organizations");
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

    private function createOrganizationDetails($pivotId, $typeSlug)
    {
        $carBrands = ['Honda', 'Toyota', 'BMW', 'Mercedes', 'Mazda', 'Nissan'];
        $carModels = ['Civic', 'Accord', 'Camry', 'Corolla', 'CX-5', 'X5', 'C-Class'];
        $colors = ['Black', 'White', 'Silver', 'Red', 'Blue', 'Grey'];

        switch ($typeSlug) {
            case 'car_club':
                \App\Models\MemberOrganizationCarDetail::create([
                    'member_organization_id' => $pivotId,
                    'car_brand' => $carBrands[array_rand($carBrands)],
                    'car_model' => $carModels[array_rand($carModels)],
                    'car_plate' => strtoupper(substr(uniqid(), -7)),
                    'car_color' => $colors[array_rand($colors)],
                    'car_year' => rand(2015, 2024),
                ]);
                break;

            case 'residential_club':
                \App\Models\MemberOrganizationResidentialDetail::create([
                    'member_organization_id' => $pivotId,
                    'unit_number' => rand(1, 50) . '-' . rand(1, 20),
                    'block' => chr(rand(65, 72)), // A-H
                    'floor' => rand(1, 25),
                    'address_line_1' => rand(1, 999) . ' Main Street',
                    'address_line_2' => 'Apartment ' . rand(1, 100),
                    'postcode' => rand(10000, 99999),
                    'city' => ['Kuala Lumpur', 'Petaling Jaya', 'Shah Alam'][array_rand(['Kuala Lumpur', 'Petaling Jaya', 'Shah Alam'])],
                    'state' => 'Selangor',
                ]);
                break;

            case 'sports_club':
                \App\Models\MemberOrganizationSportsDetail::create([
                    'member_organization_id' => $pivotId,
                    'emergency_contact_name' => 'Emergency Contact ' . rand(1, 100),
                    'emergency_contact_phone' => '+6012' . rand(1000000, 9999999),
                    'blood_type' => ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'][array_rand(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])],
                    'medical_conditions' => rand(0, 1) ? 'None' : 'Asthma',
                    'preferred_sports' => ['Football', 'Basketball', 'Tennis', 'Swimming', 'Badminton'][array_rand(['Football', 'Basketball', 'Tennis', 'Swimming', 'Badminton'])],
                ]);
                break;
        }
    }
}
