<?php

namespace Database\Seeders;

use App\Models\Languages;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = ['NL', 'FR', 'EN'];
        foreach($languages as $language){
            $lang = new Languages();
            $lang->language_code = $language;
            $lang->created_at = now();
            $lang->updated_at = now();
            $lang->save();
        }
    }
}
