<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Super Admin',
        //     'email' => 'admin@admin.com',
        //     'password'  =>  Hash::make('admin123'),
        //     'role'  =>  'superadmin'
        // ]);
        // $user = User::factory()->create([
        //     'name' => 'Ari fitness',
        //     'email' => 'arifitness@gmail.com',
        //     'password'  =>  Hash::make('ari123'),
        // ]);
        Customer::create([
            'user_id'       =>  2,
            'name'          =>  'Ari Fitness Center',
            'contact_no'    =>  '9999999999',
            'address'       =>  'Kundukulam',
            'expiry_date'   =>  now()
        ]);
    }
}
