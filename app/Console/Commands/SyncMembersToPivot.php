<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;

class SyncMembersToPivot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:sync-pivot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync members with organization_id to member_organization pivot table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting member sync to pivot table...');

        $members = Member::whereNotNull('organization_id')->get();
        $synced = 0;
        $skipped = 0;

        foreach ($members as $member) {
            $exists = DB::table('member_organization')
                ->where('member_id', $member->id)
                ->where('organization_id', $member->organization_id)
                ->exists();

            if (!$exists) {
                DB::table('member_organization')->insert([
                    'member_id' => $member->id,
                    'organization_id' => $member->organization_id,
                    'joined_at' => $member->created_at ?? now(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $synced++;
                $this->line("✓ Synced: {$member->name}");
            } else {
                $skipped++;
            }
        }

        $this->newLine();
        $this->info("Sync completed!");
        $this->info("Synced: {$synced} members");
        $this->info("Skipped: {$skipped} members (already in pivot table)");

        return Command::SUCCESS;
    }
}
