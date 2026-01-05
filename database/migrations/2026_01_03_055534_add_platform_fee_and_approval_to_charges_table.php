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
        Schema::table('charges', function (Blueprint $table) {
            $table->decimal('platform_fee_percentage', 5, 2)->nullable()->after('amount');
            $table->enum('platform_fee_operator', ['and', 'or'])->nullable()->after('platform_fee_percentage');
            $table->decimal('platform_fee_fixed', 10, 2)->nullable()->after('platform_fee_operator');
            $table->enum('approval_status', ['draft', 'pending', 'approved', 'rejected'])->default('draft')->after('status');
            $table->text('reject_reason')->nullable()->after('approval_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropColumn(['platform_fee_percentage', 'platform_fee_operator', 'platform_fee_fixed', 'approval_status', 'reject_reason']);
        });
    }
};
