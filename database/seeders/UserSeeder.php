<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\TSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use TSeeder;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateTable('users');
        $user = new User();
        $user->name = 'User';
        $user->email = 'user@gmail.com';
        $user->password = bcrypt('12345678');
        $user->save();
    }
}
