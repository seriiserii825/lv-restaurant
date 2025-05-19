<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Traits\TSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{

    use TSeeder;
    public function run(): void
    {
        $this->truncateTable('admins');

        $admin = new Admin();
        $admin->name = 'Admin';
        $admin->email = 'admin@gmail.com';
        $admin->password = bcrypt('12345678');
        $admin->role = 'admin';
        $admin->phone = '068342352';
        $admin->address = '123 Main St, City, Country';
        $admin->save();
    }
}
