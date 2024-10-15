<?php

use App\Models\Event\Schedule;
use App\Models\User;
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
        Schema::create('schedule_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Schedule::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->json('channels')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! app()->isProduction()) {
            Schema::dropIfExists('schedule_user');
        }
    }
};
