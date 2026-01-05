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
        Schema::create('member_organization_car_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_organization_id')
                ->constrained('member_organization')
                ->onDelete('cascade')
                ->name('mo_car_details_mo_id_fk');
            $table->string('car_brand')->nullable();
            $table->string('car_model')->nullable();
            $table->string('car_plate')->nullable();
            $table->string('car_color')->nullable();
            $table->integer('car_year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_organization_car_details');
    }
};
