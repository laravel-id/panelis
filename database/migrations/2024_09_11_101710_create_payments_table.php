<?php

use App\Enums\Events\PaymentStatus;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_id')->nullable();
            $table->string('vendor')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->float('price')->default(0);
            $table->float('discount')->default(0);
            $table->float('total')->default(0);
            $table->enum('status', array_column(PaymentStatus::cases(), 'value'))
                ->default(PaymentStatus::Pending->value);
            $table->dateTime('expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
