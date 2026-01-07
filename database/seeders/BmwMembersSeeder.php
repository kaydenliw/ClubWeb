<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\Member;

class BmwMembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find BMW Club Malaysia
        $bmwClub = Organization::where('name', 'BMW Club Malaysia')->first();

        if (!$bmwClub) {
            $this->command->error('BMW Club Malaysia not found!');
            return;
        }

        $members = [
            ['name' => 'Dato\' Ahmad Zaki', 'email' => 'zaki.ahmad@gmail.com', 'phone' => '+60123456021', 'car_brand' => 'BMW', 'car_model' => '740Li xDrive', 'car_plate' => 'VIP 7'],
            ['name' => 'Tan Sri Lim', 'email' => 'tslim@gmail.com', 'phone' => '+60123456022', 'car_brand' => 'BMW', 'car_model' => 'X7 M50i', 'car_plate' => 'WW 1'],
            ['name' => 'Michelle Wong', 'email' => 'michelle.wong@gmail.com', 'phone' => '+60123456023', 'car_brand' => 'BMW', 'car_model' => 'X5 xDrive40i', 'car_plate' => 'WYL 8888'],
            ['name' => 'Daniel Tan Wei Lun', 'email' => 'daniel.tan@gmail.com', 'phone' => '+60123456024', 'car_brand' => 'BMW', 'car_model' => '330i M Sport', 'car_plate' => 'WA 3388'],
            ['name' => 'Datin Siti Aminah', 'email' => 'siti.aminah@gmail.com', 'phone' => '+60123456025', 'car_brand' => 'BMW', 'car_model' => 'X3 xDrive30i', 'car_plate' => 'SAA 1'],
            ['name' => 'Steven Lim Chee Keong', 'email' => 'steven.lim@gmail.com', 'phone' => '+60123456026', 'car_brand' => 'BMW', 'car_model' => '520i M Sport', 'car_plate' => 'WKL 520'],
            ['name' => 'Jessica Lee Mei Ling', 'email' => 'jessica.lee@gmail.com', 'phone' => '+60123456027', 'car_brand' => 'BMW', 'car_model' => 'X1 sDrive20i', 'car_plate' => 'WVE 2020'],
            ['name' => 'Marcus Chong', 'email' => 'marcus.chong@gmail.com', 'phone' => '+60123456028', 'car_brand' => 'BMW', 'car_model' => 'M3 Competition', 'car_plate' => 'WKL 3M'],
            ['name' => 'Nurul Huda Binti Hassan', 'email' => 'nurul.huda@gmail.com', 'phone' => '+60123456029', 'car_brand' => 'BMW', 'car_model' => '218i Gran Coupe', 'car_plate' => 'WFT 218'],
            ['name' => 'Dr. Kumar Rajendran', 'email' => 'dr.kumar@gmail.com', 'phone' => '+60123456030', 'car_brand' => 'BMW', 'car_model' => '530i M Sport', 'car_plate' => 'WKL 5300'],
        ];

        foreach ($members as $memberData) {
            // Check if member already exists
            $exists = Member::where('email', $memberData['email'])
                ->where('organization_id', $bmwClub->id)
                ->exists();

            if (!$exists) {
                Member::create([
                    'organization_id' => $bmwClub->id,
                    'name' => $memberData['name'],
                    'email' => $memberData['email'],
                    'phone' => $memberData['phone'],
                    'car_brand' => $memberData['car_brand'],
                    'car_model' => $memberData['car_model'],
                    'car_plate' => $memberData['car_plate'],
                    'status' => 'active',
                    'synced_to_accounting' => rand(0, 1) == 1,
                    'accounting_sync_at' => rand(0, 1) == 1 ? now()->subDays(rand(1, 30)) : null,
                ]);
                $this->command->info("Created member: {$memberData['name']}");
            } else {
                $this->command->warn("Member already exists: {$memberData['name']}");
            }
        }

        $this->command->info('BMW Club members seeding completed!');
    }
}
