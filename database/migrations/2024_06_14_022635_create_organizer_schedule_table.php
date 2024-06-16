<?php

use App\Models\Event\Organizer;
use App\Models\Event\Schedule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Event the migrations.
     */
    public function up(): void
    {
        Schema::create('organizer_schedule', function (Blueprint $table) {
            $table->foreignIdFor(Organizer::class)->constrained();
            $table->foreignIdFor(Schedule::class)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizer_schedule');
    }
};
