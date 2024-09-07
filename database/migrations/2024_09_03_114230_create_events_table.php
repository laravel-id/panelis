<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $query = <<<'SQL'
        create virtual table events using fts5 (
            id,
            slug,
            title,
            description,
            location,
            region,
            categories,
            types,
            organizers,
            started_at,
            finished_at,
            is_virtual,
            is_pinned
        );
        SQL;

        DB::statement($query);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
