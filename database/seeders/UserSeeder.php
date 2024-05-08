<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker, User $user)
    {
        $new_user = new User();
        $new_user->id = 1;
        $new_user->name = 'Angelo Friello';
        $new_user->email = 'angelofriello01@gmail.com';
        $new_user->password = 'ciao';
        $new_user->save();
       
    }
}
