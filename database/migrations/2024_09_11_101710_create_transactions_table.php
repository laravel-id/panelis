<?php

use App\Enums\Transaction\TransactionStatus;
use App\Models\Transaction\Bank;
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
        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)
                ->nullable()
                ->constrained();
            $table->foreignIdFor(Bank::class)
                ->nullable()
                ->constrained();
            $table->ulid();
            $table->string('vendor_id')->nullable();
            $table->string('vendor')->nullable();
            $table->morphs('transactionable');
            $table->float('total')->default(0);
            $table->enum('status', array_column(TransactionStatus::cases(), 'value'))
                ->default(TransactionStatus::Pending->value);
            $table->json('metadata');
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
        Schema::dropIfExists('transactions');
    }
};
