<?php

use App\Models\Location\District;
use App\Models\User;
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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained();
            $table->foreignIdFor(District::class)->nullable()->constrained();
            $table->string('slug')->unique();
            $table->string('poster')->nullable();
            $table->string('title');
            $table->text('description');
            $table->json('categories');
            $table->dateTime('started_at');
            $table->dateTime('finished_at')->nullable();
            $table->boolean('is_virtual')->default(false);
            $table->string('location')->nullable();
            $table->json('contacts');
            $table->string('url')->nullable();
            $table->json('metadata');
            $table->timestamps();
            $table->softDeletes();
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
