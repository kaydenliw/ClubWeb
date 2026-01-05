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
        Schema::create('member_organization_sports_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_organization_id')
                ->constrained('member_organization')
                ->onDelete('cascade')
                ->name('mo_sports_details_mo_id_fk');
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('blood_type')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->json('preferred_sports')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_organization_sports_details');
    }
};
