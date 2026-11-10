<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Database\Seeder;

/** Menus and site settings (phone, email, address, socials, footer text). */
class SiteSeeder extends Seeder
{
    use ReadsSeedData;

    public function run(): void
    {
        foreach ($this->data('menus') as $m) {
            $menu = Menu::updateOrCreate(['name' => $m['name']]);
            $menu->items()->delete();
            foreach ($m['items'] as $item) {
                $menu->items()->create($item);
            }
        }

        foreach ($this->data('settings') as $s) {
            Setting::updateOrCreate(['key' => $s['key']], ['value' => $s['value'], 'group' => $s['group']]);
        }
    }
}
