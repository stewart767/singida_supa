<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'university_name', 'value' => 'SUPA / OUT University', 'group' => 'general', 'type' => 'string'],
            ['key' => 'direct_entry_min_gpa', 'value' => '3.0', 'group' => 'business_rules', 'type' => 'float'],
            ['key' => 'direct_entry_min_points', 'value' => '5', 'group' => 'business_rules', 'type' => 'integer'],
            ['key' => 'application_fee_default', 'value' => '20000.00', 'group' => 'finance', 'type' => 'float'],
            ['key' => 'currency_symbol', 'value' => 'TZS', 'group' => 'finance', 'type' => 'string'],
            ['key' => 'support_email', 'value' => 'admissions@supa.ac.tz', 'group' => 'contact', 'type' => 'string'],
            ['key' => 'support_phone', 'value' => '+255 22 266 8820', 'group' => 'contact', 'type' => 'string'],
            ['key' => 'top_announcement_badge', 'value' => '2026/2027', 'group' => 'general', 'type' => 'string'],
            ['key' => 'top_announcement_text', 'value' => 'Online Admissions Now Open for Undergraduate & Postgraduate Programmes', 'group' => 'general', 'type' => 'string'],
            ['key' => 'top_announcement_link_text', 'value' => 'Track Application Status', 'group' => 'general', 'type' => 'string'],
            ['key' => 'top_announcement_link_url', 'value' => '', 'group' => 'general', 'type' => 'string'],
            ['key' => 'top_announcement_phone', 'value' => '+255 22 266 8820', 'group' => 'general', 'type' => 'string'],
        ];

        foreach ($settings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }
    }
}
