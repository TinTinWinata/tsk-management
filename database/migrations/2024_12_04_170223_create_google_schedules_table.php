<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('google_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->dateTime('date');
            $table->string('title');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('google_event_id');
            $table->uuid('schedule_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_schedules');
    }
};
