<?php

use App\Filament\Resources\Event\ScheduleResource\Enums\PackagePriceType;
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
        Schema::table('packages', function (Blueprint $table): void {
            $table->string('price_type')
                ->default(PackagePriceType::Normal->value)
                ->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table): void {
            $table->dropColumn('price_type');
        });
    }
};
