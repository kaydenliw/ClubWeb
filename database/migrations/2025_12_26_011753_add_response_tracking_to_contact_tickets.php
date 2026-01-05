<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_tickets', function (Blueprint $table) {
            $table->timestamp('first_response_at')->nullable()->after('replied_at');
            $table->timestamp('resolved_at')->nullable()->after('first_response_at');
            $table->integer('first_response_time_minutes')->nullable()->after('resolved_at');
            $table->integer('resolution_time_minutes')->nullable()->after('first_response_time_minutes');
            $table->string('assigned_to')->nullable()->after('resolution_time_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('contact_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'first_response_at',
                'resolved_at',
                'first_response_time_minutes',
                'resolution_time_minutes',
                'assigned_to'
            ]);
        });
    }
};
