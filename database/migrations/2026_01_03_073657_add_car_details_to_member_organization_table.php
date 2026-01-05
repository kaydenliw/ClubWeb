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
            $table->string('car_brand')->nullable()->after('notes');
            $table->string('car_model')->nullable()->after('car_brand');
            $table->string('car_plate')->nullable()->after('car_model');
            $table->string('car_color')->nullable()->after('car_plate');
            $table->integer('car_year')->nullable()->after('car_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_organization', function (Blueprint $table) {
            $table->dropColumn(['car_brand', 'car_model', 'car_plate', 'car_color', 'car_year']);
        });
    }
};
