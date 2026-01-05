<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('organization_id')->constrained('users')->nullOnDelete();
            $table->timestamp('publish_date')->nullable()->after('is_published');
            $table->enum('approval_status', ['draft', 'pending_approval', 'approved_pending_publish', 'approved_published', 'rejected'])->default('draft')->after('publish_date');
            $table->text('reject_reason')->nullable()->after('approval_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'publish_date', 'approval_status', 'reject_reason']);
        });
    }
};
