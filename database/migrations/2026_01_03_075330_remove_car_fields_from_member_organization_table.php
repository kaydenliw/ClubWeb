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
        Schema::table('member_organization', function (Blueprint $table) {
            $table->dropColumn(['car_brand', 'car_model', 'car_plate', 'car_color', 'car_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_organization', function (Blueprint $table) {
            $table->string('car_brand')->nullable();
            $table->string('car_model')->nullable();
            $table->string('car_plate')->nullable();
            $table->string('car_color')->nullable();
            $table->integer('car_year')->nullable();
        });
    }
};
