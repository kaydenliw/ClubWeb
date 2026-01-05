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
            $table->string('pic_name')->nullable()->after('phone');
            $table->decimal('platform_fee_percentage', 5, 2)->nullable()->after('status');
            $table->enum('platform_fee_operator', ['and', 'or'])->nullable()->after('platform_fee_percentage');
            $table->decimal('platform_fee_fixed', 10, 2)->nullable()->after('platform_fee_operator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['pic_name', 'platform_fee_percentage', 'platform_fee_operator', 'platform_fee_fixed']);
        });
    }
};
