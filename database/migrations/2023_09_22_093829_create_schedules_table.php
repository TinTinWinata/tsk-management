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
        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->dateTime('date');
            $table->string('title');
            $table->integer('position')->nullable();
            $table->boolean('is_done');
            $table->uuid('scheduleable_id');
            $table->string('scheduleable_type');
            $table->index(['scheduleable_id', 'scheduleable_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
