<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->first_name = "Milan";
        $user->last_name = "Vandenbussche";
        $user->admin = true;
        $user->language_id = 1;
        $user->email = "vandenbussche.milan@gmail.com";
        $user->password = bcrypt('MilanVandenbussche');
        if($user->save()){
            $user->tags()->attach([2, 3]);
        }

        User::factory()->count(50)->create()->each(function (User $user) {
            $user->tags()->attach([1]);
        });
    }
}
