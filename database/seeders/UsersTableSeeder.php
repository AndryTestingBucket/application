<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->id = 1;
        $user->name = 'Admin';
        $user->email = 'admin@mail.ru';
        $user->email_verified_at = '15.10.2022';
        $user->password = Hash::make('admin');
        $user->token = Hash::make('dplp31qppIkvoxr3lIqsX77BrUrhDhsg9GFk9atO');
        $user->remember_token = NULL;
        $user->created_at = now();
        $user->updated_at = now();
        $user->save();
    }
}
