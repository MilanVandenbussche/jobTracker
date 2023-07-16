<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            "users" => ["new", "recruiter", "headhunter", "applicant"],
            'jobs' => ["new", "tech", "design", "other"]
        ];
        foreach ($tags as $model => $tagNames){
            foreach($tagNames as $name){
                $tag = new Tag();
                $tag->name = $name;
                $tag->for = $model;
                $tag->created_at = now();
                $tag->updated_at = now();
                $tag->save();
            }
        }
    }
}
