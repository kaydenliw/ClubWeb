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
        Schema::create('member_organization_residential_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_organization_id')
                ->constrained('member_organization')
                ->onDelete('cascade')
                ->name('mo_residential_details_mo_id_fk');
            $table->string('unit_number')->nullable();
            $table->string('block')->nullable();
            $table->string('floor')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_organization_residential_details');
    }
};
