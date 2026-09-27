<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Point existing installs at the portfolio branding (the "0" logo set).
 *
 * Databases created from the starter template still reference its logo
 * (base-logo.png) and indigo theme colour. Values an admin has changed
 * since (e.g. an uploaded logo) are left alone.
 */
return new class extends Migration
{
    public function up(): void
    {
        $replace = [
            'site_logo' => [['base-logo.png', 'assets/media/branding/logo-mark.svg', ''], 'assets/media/branding/logo-tile.png'],
            'site_favicon' => [['', 'favicon.ico'], 'assets/media/branding/favicon.ico'],
            'site_og_image' => [[''], 'assets/media/branding/og-image.png'],
            'site_theme_color' => [['#4f46e5', ''], '#06060c'],
        ];

        foreach ($replace as $key => [$templateValues, $value]) {
            $current = DB::table('settings')->where('key', $key)->value('value');

            if ($current === null) {
                DB::table('settings')->insert(['key' => $key, 'value' => $value, 'created_at' => now(), 'updated_at' => now()]);
            } elseif (in_array($current, $templateValues, true)) {
                DB::table('settings')->where('key', $key)->update(['value' => $value, 'updated_at' => now()]);
            }
        }

        // Settings are cached; make the new values visible immediately.
        Cache::forget('settings.all');
        foreach (array_keys($replace) as $key) {
            Cache::forget("setting.{$key}");
        }
    }

    public function down(): void
    {
        // Branding values are content; nothing to roll back.
    }
};
