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
        Schema::create('raspisanies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events');
            $table->foreignId('room_id')->constrained('rooms');
            $table->timestampTz('start_date')->comment('Дата начала события в формате ISO 8601 (UTC)');
            $table->timestampTz('end_date')->comment('Дата конца события в формате ISO 8601 (UTC)');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raspisanies');
    }
};
