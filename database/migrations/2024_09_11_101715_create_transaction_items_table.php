<?php

use App\Models\Transaction\Transaction;
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
        Schema::create('transaction_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Transaction::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->decimal('discount', 15)->default(0);
            $table->decimal('price', 15)->default(0);
            $table->decimal('total', 15);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
