<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clean up programmes table
        $programmes = DB::table('programmes')->get();
        foreach ($programmes as $programme) {
            $img = $programme->image;
            if ($img && Str::startsWith($img, 'http')) {
                if (preg_match('/\/storage\/(.+)$/', $img, $matches)) {
                    DB::table('programmes')
                        ->where('id', $programme->id)
                        ->update(['image' => $matches[1]]);
                }
            }
        }

        // 2. Clean up cms_hero_sliders settings
        $setting = DB::table('settings')->where('key', 'cms_hero_sliders')->first();
        if ($setting && $setting->value) {
            $sliders = json_decode($setting->value, true);
            if (is_array($sliders)) {
                $updated = false;
                foreach ($sliders as &$slider) {
                    if (isset($slider['image']) && Str::startsWith($slider['image'], 'http')) {
                        if (preg_match('/\/storage\/(.+)$/', $slider['image'], $matches)) {
                            $slider['image'] = $matches[1];
                            $updated = true;
                        }
                    }
                }
                if ($updated) {
                    DB::table('settings')
                        ->where('key', 'cms_hero_sliders')
                        ->update(['value' => json_encode($sliders)]);
                    
                    Cache::forget('setting.cms_hero_sliders');
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback is necessary as relative paths are fully compatible and preferred.
    }
};
