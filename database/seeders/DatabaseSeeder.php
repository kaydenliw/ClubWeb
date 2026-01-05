<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use App\Models\Member;
use App\Models\Charge;
use App\Models\Transaction;
use App\Models\Settlement;
use App\Models\Announcement;
use App\Models\FAQ;
use App\Models\ContactTicket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // Malaysian Car Communities
        $organizations = [
            [
                'name' => 'Proton Owners Club Malaysia',
                'logo' => null,
                'email' => 'admin@protonclub.my',
                'phone' => '+60123456789',
                'address' => 'No. 45, Jalan Sultan Ismail, 50250 Kuala Lumpur',
                'bank_name' => 'Maybank',
                'bank_account_number' => '514234567890',
                'bank_account_holder' => 'Proton Owners Club Malaysia',
                'status' => 'active',
            ],
            [
                'name' => 'Perodua Enthusiasts MY',
                'logo' => null,
                'email' => 'info@peroduaclub.my',
                'phone' => '+60198765432',
                'address' => 'Lot 123, Jalan Ampang, 50450 Kuala Lumpur',
                'bank_name' => 'CIMB Bank',
                'bank_account_number' => '800123456789',
                'bank_account_holder' => 'Perodua Enthusiasts MY',
                'status' => 'active',
            ],
            [
                'name' => 'Honda Club Malaysia',
                'logo' => null,
                'email' => 'contact@hondaclub.my',
                'phone' => '+60167891234',
                'address' => '88, Jalan Bukit Bintang, 55100 Kuala Lumpur',
                'bank_name' => 'Public Bank',
                'bank_account_number' => '312345678901',
                'bank_account_holder' => 'Honda Club Malaysia',
                'status' => 'active',
            ],
            [
                'name' => 'Toyota Owners Malaysia',
                'logo' => null,
                'email' => 'hello@toyotaowners.my',
                'phone' => '+60134567890',
                'address' => 'No. 56, Jalan Tun Razak, 50400 Kuala Lumpur',
                'bank_name' => 'RHB Bank',
                'bank_account_number' => '212345678901',
                'bank_account_holder' => 'Toyota Owners Malaysia',
                'status' => 'active',
            ],
            [
                'name' => 'BMW Club Malaysia',
                'logo' => null,
                'email' => 'admin@bmwclub.my',
                'phone' => '+60187654321',
                'address' => 'Suite 12-3, Menara Axis, Jalan 51A/223, 46100 Petaling Jaya',
                'bank_name' => 'Hong Leong Bank',
                'bank_account_number' => '123456789012',
                'bank_account_holder' => 'BMW Club Malaysia',
                'status' => 'active',
            ],
        ];

        foreach ($organizations as $index => $orgData) {
            $org = Organization::create($orgData);

            // Create organization admin
            User::create([
                'name' => $this->getAdminName($index),
                'email' => $this->getAdminEmail($index),
                'password' => Hash::make('password'),
                'role' => 'organization_admin',
                'organization_id' => $org->id,
            ]);

            // Create members for each organization
            $this->createMembers($org, $index);

            // Create charges/donations
            $this->createCharges($org, $index);

            // Create FAQs
            $this->createFAQs($org);

            // Create announcements
            $this->createAnnouncements($org);
        }

        // Create transactions after all organizations and members are created
        $this->createTransactions();

        // Create contact tickets
        $this->createContactTickets();

        // Create settlements
        $this->createSettlements();
    }

    private function getAdminName($index)
    {
        $names = [
            'Ahmad Razak',
            'Siti Nurhaliza',
            'Tan Wei Ming',
            'Kumar Selvam',
            'Lee Chong Wei',
        ];
        return $names[$index];
    }

    private function getAdminEmail($index)
    {
        $emails = [
            'ahmad@protonclub.my',
            'siti@peroduaclub.my',
            'wei@hondaclub.my',
            'kumar@toyotaowners.my',
            'lee@bmwclub.my',
        ];
        return $emails[$index];
    }

    private function createMembers($org, $orgIndex)
    {
        $memberData = $this->getMemberData($orgIndex);

        foreach ($memberData as $data) {
            Member::create([
                'organization_id' => $org->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'car_brand' => $data['car_brand'],
                'car_model' => $data['car_model'],
                'car_plate' => $data['car_plate'],
                'status' => 'active',
                'synced_to_accounting' => rand(0, 1) == 1,
                'accounting_sync_at' => rand(0, 1) == 1 ? now()->subDays(rand(1, 30)) : null,
            ]);
        }
    }

    private function getMemberData($orgIndex)
    {
        $allMembers = [
            // Proton Club Members
            [
                ['name' => 'Mohd Hafiz', 'email' => 'hafiz@gmail.com', 'phone' => '+60123456001', 'car_brand' => 'Proton', 'car_model' => 'X70', 'car_plate' => 'WXY 1234'],
                ['name' => 'Nurul Ain', 'email' => 'nurul@gmail.com', 'phone' => '+60123456002', 'car_brand' => 'Proton', 'car_model' => 'X50', 'car_plate' => 'ABC 5678'],
                ['name' => 'Azman Ibrahim', 'email' => 'azman@gmail.com', 'phone' => '+60123456003', 'car_brand' => 'Proton', 'car_model' => 'Saga', 'car_plate' => 'DEF 9012'],
                ['name' => 'Farah Liyana', 'email' => 'farah@gmail.com', 'phone' => '+60123456004', 'car_brand' => 'Proton', 'car_model' => 'Persona', 'car_plate' => 'GHI 3456'],
                ['name' => 'Rizal Ahmad', 'email' => 'rizal@gmail.com', 'phone' => '+60123456005', 'car_brand' => 'Proton', 'car_model' => 'Iriz', 'car_plate' => 'JKL 7890'],
            ],
            // Perodua Club Members
            [
                ['name' => 'Lim Mei Ling', 'email' => 'meiling@gmail.com', 'phone' => '+60123456006', 'car_brand' => 'Perodua', 'car_model' => 'Myvi', 'car_plate' => 'MNO 1234'],
                ['name' => 'Wong Kar Wai', 'email' => 'karwai@gmail.com', 'phone' => '+60123456007', 'car_brand' => 'Perodua', 'car_model' => 'Axia', 'car_plate' => 'PQR 5678'],
                ['name' => 'Tan Siew Lan', 'email' => 'siewlan@gmail.com', 'phone' => '+60123456008', 'car_brand' => 'Perodua', 'car_model' => 'Alza', 'car_plate' => 'STU 9012'],
                ['name' => 'Chen Wei Jie', 'email' => 'weijie@gmail.com', 'phone' => '+60123456009', 'car_brand' => 'Perodua', 'car_model' => 'Bezza', 'car_plate' => 'VWX 3456'],
                ['name' => 'Ng Ai Ling', 'email' => 'ailing@gmail.com', 'phone' => '+60123456010', 'car_brand' => 'Perodua', 'car_model' => 'Aruz', 'car_plate' => 'YZA 7890'],
            ],
            // Honda Club Members
            [
                ['name' => 'Rajesh Kumar', 'email' => 'rajesh@gmail.com', 'phone' => '+60123456011', 'car_brand' => 'Honda', 'car_model' => 'Civic', 'car_plate' => 'BCD 1234'],
                ['name' => 'Priya Devi', 'email' => 'priya@gmail.com', 'phone' => '+60123456012', 'car_brand' => 'Honda', 'car_model' => 'City', 'car_plate' => 'EFG 5678'],
                ['name' => 'Suresh Nair', 'email' => 'suresh@gmail.com', 'phone' => '+60123456013', 'car_brand' => 'Honda', 'car_model' => 'Accord', 'car_plate' => 'HIJ 9012'],
                ['name' => 'Kavitha Rao', 'email' => 'kavitha@gmail.com', 'phone' => '+60123456014', 'car_brand' => 'Honda', 'car_model' => 'CR-V', 'car_plate' => 'KLM 3456'],
                ['name' => 'Anand Krishnan', 'email' => 'anand@gmail.com', 'phone' => '+60123456015', 'car_brand' => 'Honda', 'car_model' => 'HR-V', 'car_plate' => 'NOP 7890'],
            ],
            // Toyota Club Members
            [
                ['name' => 'Sarah Abdullah', 'email' => 'sarah@gmail.com', 'phone' => '+60123456016', 'car_brand' => 'Toyota', 'car_model' => 'Vios', 'car_plate' => 'QRS 1234'],
                ['name' => 'Ismail Hassan', 'email' => 'ismail@gmail.com', 'phone' => '+60123456017', 'car_brand' => 'Toyota', 'car_model' => 'Camry', 'car_plate' => 'TUV 5678'],
                ['name' => 'Zainab Yusof', 'email' => 'zainab@gmail.com', 'phone' => '+60123456018', 'car_brand' => 'Toyota', 'car_model' => 'Hilux', 'car_plate' => 'WXY 9012'],
                ['name' => 'Kamal Ariffin', 'email' => 'kamal@gmail.com', 'phone' => '+60123456019', 'car_brand' => 'Toyota', 'car_model' => 'Fortuner', 'car_plate' => 'ZAB 3456'],
                ['name' => 'Nadia Aziz', 'email' => 'nadia@gmail.com', 'phone' => '+60123456020', 'car_brand' => 'Toyota', 'car_model' => 'Yaris', 'car_plate' => 'CDE 7890'],
            ],
            // BMW Club Members
            [
                ['name' => 'Daniel Tan', 'email' => 'daniel@gmail.com', 'phone' => '+60123456021', 'car_brand' => 'BMW', 'car_model' => '320i', 'car_plate' => 'FGH 1234'],
                ['name' => 'Michelle Wong', 'email' => 'michelle@gmail.com', 'phone' => '+60123456022', 'car_brand' => 'BMW', 'car_model' => 'X5', 'car_plate' => 'IJK 5678'],
                ['name' => 'Steven Lim', 'email' => 'steven@gmail.com', 'phone' => '+60123456023', 'car_brand' => 'BMW', 'car_model' => '520i', 'car_plate' => 'LMN 9012'],
                ['name' => 'Jessica Lee', 'email' => 'jessica@gmail.com', 'phone' => '+60123456024', 'car_brand' => 'BMW', 'car_model' => 'X3', 'car_plate' => 'OPQ 3456'],
                ['name' => 'Marcus Chong', 'email' => 'marcus@gmail.com', 'phone' => '+60123456025', 'car_brand' => 'BMW', 'car_model' => 'M3', 'car_plate' => 'RST 7890'],
            ],
        ];

        return $allMembers[$orgIndex];
    }

    private function createCharges($org, $orgIndex)
    {
        $charges = [
            [
                'title' => 'Annual Membership Fee',
                'description' => 'Yearly membership subscription for all club benefits and activities',
                'amount' => 150.00,
                'type' => 'charge',
                'is_recurring' => true,
                'recurring_months' => 12,
                'recurring_frequency' => 'annually',
                'status' => 'active',
                'approval_status' => 'approved',
                'platform_fee_percentage' => 5.0,
            ],
            [
                'title' => 'Monthly Meetup Fee',
                'description' => 'Monthly gathering and activities including breakfast and venue',
                'amount' => 30.00,
                'type' => 'charge',
                'is_recurring' => true,
                'recurring_months' => 1,
                'recurring_frequency' => 'monthly',
                'status' => 'active',
                'approval_status' => 'approved',
                'platform_fee_percentage' => 5.0,
            ],
            [
                'title' => 'Track Day Registration',
                'description' => 'Sepang Circuit track day event - Full day access with professional instructors',
                'amount' => 250.00,
                'type' => 'charge',
                'is_recurring' => false,
                'recurring_frequency' => 'one-time',
                'status' => 'active',
                'approval_status' => 'pending',
            ],
            [
                'title' => 'Club Merchandise',
                'description' => 'Official club t-shirts, stickers, caps and accessories',
                'amount' => 80.00,
                'type' => 'charge',
                'is_recurring' => false,
                'recurring_frequency' => 'one-time',
                'status' => 'active',
                'approval_status' => 'approved',
                'platform_fee_percentage' => 3.0,
                'platform_fee_fixed' => 2.00,
                'platform_fee_operator' => 'and',
            ],
            [
                'title' => 'Charity Drive Donation',
                'description' => 'Support local community initiatives and underprivileged families',
                'amount' => 50.00,
                'type' => 'donation',
                'is_recurring' => false,
                'recurring_frequency' => 'one-time',
                'status' => 'inactive',
                'approval_status' => 'pending',
            ],
        ];

        foreach ($charges as $chargeData) {
            Charge::create(array_merge([
                'organization_id' => $org->id,
                'image' => null,
            ], $chargeData));
        }
    }

    private function createFAQs($org)
    {
        $faqs = [
            ['question' => 'How do I join the club?', 'answer' => 'You can join by registering through our mobile app or contacting our admin directly. Annual membership fee applies.', 'order' => 1],
            ['question' => 'What are the membership benefits?', 'answer' => 'Members enjoy exclusive access to events, discounts on merchandise, technical support, and networking opportunities with fellow enthusiasts.', 'order' => 2],
            ['question' => 'How often are meetups organized?', 'answer' => 'We organize monthly meetups on the first Saturday of each month. Special events like track days are announced in advance.', 'order' => 3],
            ['question' => 'Can I bring guests to events?', 'answer' => 'Yes, members can bring up to 2 guests per event. Guest fees may apply depending on the event type.', 'order' => 4],
            ['question' => 'How do I make payments?', 'answer' => 'Payments can be made via online banking, credit card, or e-wallet through our mobile app. Cash payments are accepted at physical events.', 'order' => 5],
        ];

        foreach ($faqs as $faqData) {
            FAQ::create([
                'organization_id' => $org->id,
                'question' => $faqData['question'],
                'answer' => $faqData['answer'],
                'order' => $faqData['order'],
            ]);
        }
    }

    private function createAnnouncements($org)
    {
        $announcements = [
            [
                'title' => 'Welcome to Our Community!',
                'content' => 'We are excited to have you join our car enthusiast community. Stay tuned for upcoming events and activities!',
                'approval_status' => 'approved_published',
                'is_published' => true,
                'publish_date' => now()->subDays(30),
                'published_at' => now()->subDays(30),
            ],
            [
                'title' => 'Monthly Meetup - January 2026',
                'content' => 'Join us for our monthly meetup at Pavilion KL parking lot. Coffee, conversations, and car showcases! Date: 4th January 2026, Time: 9:00 AM',
                'approval_status' => 'approved_published',
                'is_published' => true,
                'publish_date' => now()->subDays(15),
                'published_at' => now()->subDays(15),
            ],
            [
                'title' => 'Track Day Event - Sepang Circuit',
                'content' => 'Gear up for an exciting track day at Sepang International Circuit! Limited slots available. Registration closes on 15th January 2026.',
                'approval_status' => 'approved_pending_publish',
                'is_published' => false,
                'publish_date' => now()->addDays(5),
                'published_at' => null,
            ],
            [
                'title' => 'New Merchandise Available',
                'content' => 'Check out our latest club merchandise including t-shirts, caps, and stickers!',
                'approval_status' => 'pending_approval',
                'is_published' => false,
                'publish_date' => null,
                'published_at' => null,
            ],
            [
                'title' => 'Annual General Meeting 2026',
                'content' => 'Our AGM will be held on 20th February 2026. All members are encouraged to attend.',
                'approval_status' => 'draft',
                'is_published' => false,
                'publish_date' => null,
                'published_at' => null,
            ],
            [
                'title' => 'Charity Drive Announcement',
                'content' => 'Join us in our charity drive to support underprivileged communities.',
                'approval_status' => 'rejected',
                'is_published' => false,
                'publish_date' => null,
                'published_at' => null,
                'reject_reason' => 'Please provide more details about the charity organization and how funds will be used.',
            ],
        ];

        foreach ($announcements as $announcementData) {
            Announcement::create(array_merge(['organization_id' => $org->id], $announcementData));
        }
    }

    private function createTransactions()
    {
        $members = Member::all();
        $charges = Charge::all();
        $paymentMethods = ['cash', 'card', 'bank_transfer', 'online'];
        $statuses = ['completed', 'completed', 'completed', 'pending', 'failed'];

        foreach ($members as $member) {
            // Create 2-3 old transactions (last 2-3 months)
            $oldTransactionCount = rand(2, 3);
            for ($i = 0; $i < $oldTransactionCount; $i++) {
                $charge = $charges->where('organization_id', $member->organization_id)->random();
                $status = $statuses[array_rand($statuses)];
                $type = rand(0, 10) > 8 ? 'refund' : 'payment';
                $amount = $type === 'refund' ? -$charge->amount : $charge->amount;
                $platformFee = abs($amount) * 0.05; // 5% platform fee

                Transaction::create([
                    'organization_id' => $member->organization_id,
                    'member_id' => $member->id,
                    'charge_id' => $charge->id,
                    'transaction_number' => 'TXN' . strtoupper(uniqid()),
                    'amount' => $amount,
                    'platform_fee' => $platformFee,
                    'type' => $type,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'status' => $status,
                    'notes' => $type === 'refund' ? 'Refund processed' : null,
                    'synced_to_accounting' => $status === 'completed' ? (rand(0, 1) == 1) : false,
                    'synced_at' => $status === 'completed' && rand(0, 1) == 1 ? now()->subDays(rand(1, 20)) : null,
                    'created_at' => now()->subMonths(rand(2, 3))->subDays(rand(1, 28)),
                ]);
            }

            // Create 2-5 transactions THIS MONTH for realistic dashboard data
            $thisMonthCount = rand(2, 5);
            for ($i = 0; $i < $thisMonthCount; $i++) {
                $charge = $charges->where('organization_id', $member->organization_id)->random();
                $status = 'completed'; // Most current month transactions are completed
                $type = 'payment';
                $platformFee = $charge->amount * 0.05; // 5% platform fee

                Transaction::create([
                    'organization_id' => $member->organization_id,
                    'member_id' => $member->id,
                    'charge_id' => $charge->id,
                    'transaction_number' => 'TXN' . strtoupper(uniqid()),
                    'amount' => $charge->amount,
                    'platform_fee' => $platformFee,
                    'type' => $type,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'status' => $status,
                    'notes' => null,
                    'synced_to_accounting' => rand(0, 1) == 1,
                    'synced_at' => rand(0, 1) == 1 ? now()->subDays(rand(1, 5)) : null,
                    'created_at' => now()->startOfMonth()->addDays(rand(0, now()->day - 1)),
                ]);
            }
        }
    }

    private function createContactTickets()
    {
        $members = Member::all();
        $tickets = [
            [
                'subject' => 'Payment Issue',
                'message' => 'I am having trouble making payment through the app. Can you please assist?',
                'priority' => 'high',
                'category' => 'Payment',
            ],
            [
                'subject' => 'Event Registration Question',
                'message' => 'When is the next track day event? I would like to register.',
                'priority' => 'medium',
                'category' => 'General',
            ],
            [
                'subject' => 'Membership Renewal',
                'message' => 'My membership is expiring soon. How do I renew it?',
                'priority' => 'medium',
                'category' => 'Account',
            ],
            [
                'subject' => 'Technical Support',
                'message' => 'The app is not loading properly on my device. Please help.',
                'priority' => 'urgent',
                'category' => 'Technical',
            ],
            [
                'subject' => 'Merchandise Inquiry',
                'message' => 'Do you have club t-shirts in size XL available?',
                'priority' => 'low',
                'category' => 'General',
            ],
        ];

        $priorities = ['low', 'medium', 'high', 'urgent'];
        $categories = ['General', 'Payment', 'Technical', 'Account', 'Other'];
        $statuses = ['open', 'replied', 'closed'];

        foreach ($members->random(15) as $member) {
            $ticketData = $tickets[array_rand($tickets)];
            $status = $statuses[array_rand($statuses)];

            ContactTicket::create([
                'organization_id' => $member->organization_id,
                'member_id' => $member->id,
                'ticket_number' => 'TKT' . strtoupper(uniqid()),
                'subject' => $ticketData['subject'],
                'message' => $ticketData['message'],
                'priority' => $ticketData['priority'],
                'category' => $ticketData['category'],
                'status' => $status,
                'reply' => $status === 'replied' || $status === 'closed' ? 'Thank you for contacting us. We have resolved your issue. Please let us know if you need further assistance.' : null,
                'replied_at' => $status === 'replied' || $status === 'closed' ? now()->subDays(rand(1, 10)) : null,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }

    private function createSettlements()
    {
        $organizations = Organization::all();

        foreach ($organizations as $index => $org) {
            // Create 1-2 old completed settlements
            for ($i = 0; $i < rand(1, 2); $i++) {
                $amount = rand(5000, 15000);
                $completedDate = now()->subMonths(rand(1, 3));

                Settlement::create([
                    'organization_id' => $org->id,
                    'settlement_number' => 'STL' . strtoupper(uniqid()),
                    'amount' => $amount,
                    'settlement_date' => $completedDate->toDateString(),
                    'scheduled_date' => $completedDate->toDateString(),
                    'status' => 'completed',
                    'completed_at' => $completedDate,
                    'notes' => 'Settlement transferred to bank account',
                    'created_at' => $completedDate->subDays(5),
                ]);
            }

            // Create 1 recent settlement with varied status
            $statuses = ['completed', 'pending', 'pending', 'processing'];
            $status = $statuses[$index % count($statuses)];
            $amount = rand(3000, 12000);

            if ($status === 'completed') {
                $completedDate = now()->subDays(rand(1, 10));
                Settlement::create([
                    'organization_id' => $org->id,
                    'settlement_number' => 'STL' . strtoupper(uniqid()),
                    'amount' => $amount,
                    'settlement_date' => $completedDate->toDateString(),
                    'scheduled_date' => $completedDate->toDateString(),
                    'status' => 'completed',
                    'completed_at' => $completedDate,
                    'notes' => 'Settlement transferred successfully',
                    'created_at' => $completedDate->subDays(3),
                ]);
            } else {
                $scheduledDate = now()->addDays(rand(5, 15));
                Settlement::create([
                    'organization_id' => $org->id,
                    'settlement_number' => 'STL' . strtoupper(uniqid()),
                    'amount' => $amount,
                    'settlement_date' => null,
                    'scheduled_date' => $scheduledDate->toDateString(),
                    'status' => $status,
                    'completed_at' => null,
                    'notes' => 'Awaiting processing',
                    'created_at' => now()->subDays(rand(1, 5)),
                ]);
            }
        }
    }
}
