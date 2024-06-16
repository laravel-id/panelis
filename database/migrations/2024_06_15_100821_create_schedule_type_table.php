<?php

use App\Models\Event\Schedule;
use App\Models\Event\Type;
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
        Schema::create('schedule_type', function (Blueprint $table) {
            $table->foreignIdFor(Schedule::class)->constrained();
            $table->foreignIdFor(Type::class)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_type');
    }
};
