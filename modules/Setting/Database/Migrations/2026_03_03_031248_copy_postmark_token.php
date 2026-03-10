<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Setting\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $postmarkToken = Setting::get('services.postmark.token');
        if (! empty($postmarkToken)) {
            Setting::set('services.postmark.key', $postmarkToken);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::query()
            ->where('key', 'services.postmark.key')
            ->delete();
    }
};
