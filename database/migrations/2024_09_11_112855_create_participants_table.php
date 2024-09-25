<?php

use App\Enums\Participants\BloodType;
use App\Enums\Participants\Gender;
use App\Enums\Participants\IdentityType;
use App\Enums\Participants\Status;
use App\Models\Event\Package;
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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Schedule::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Package::class)->constrained()->cascadeOnDelete();
            $table->ulid()->unique();
            $table->string('bib')->nullable();
            $table->enum('id_type', array_column(IdentityType::cases(), 'value'))->default(IdentityType::KTP->value);
            $table->string('id_number');
            $table->string('name');
            $table->date('birthdate');
            $table->enum('gender', array_column(Gender::cases(), 'value'))->default(Gender::Male->value);
            $table->enum('blood_type', array_column(BloodType::cases(), 'value'))->nullable();
            $table->string('phone');
            $table->string('email');
            $table->string('emergency_name');
            $table->string('emergency_phone');
            $table->string('emergency_relation');
            $table->enum('status', array_column(Status::cases(), 'value'))->default(Status::Pending->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
