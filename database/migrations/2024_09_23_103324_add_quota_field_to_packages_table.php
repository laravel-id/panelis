<?php

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
        Schema::table('packages', function (Blueprint $table) {
            $table->smallInteger('quota')
                ->default(0)
                ->after('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! app()->isProduction()) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('quota');
            });
        }
    }
};
