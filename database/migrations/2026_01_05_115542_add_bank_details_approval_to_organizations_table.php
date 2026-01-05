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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('pending_bank_name')->nullable()->after('bank_account_holder');
            $table->string('pending_bank_account_number')->nullable()->after('pending_bank_name');
            $table->string('pending_bank_account_holder')->nullable()->after('pending_bank_account_number');
            $table->enum('bank_details_status', ['approved', 'pending', 'rejected'])->default('approved')->after('pending_bank_account_holder');
            $table->text('bank_details_reject_reason')->nullable()->after('bank_details_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'pending_bank_name',
                'pending_bank_account_number',
                'pending_bank_account_holder',
                'bank_details_status',
                'bank_details_reject_reason'
            ]);
        });
    }
};
