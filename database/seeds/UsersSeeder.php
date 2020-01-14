<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (User::create([
            'name' => "Admin",
            'email' => "admin@evertec.com",
            'phone' => "+5713795628",
            'password' => bcrypt('password'),
        ]))->assignRole("SuperAdministrator");
        (User::create([
            'name' => "Admin Ordenes",
            'email' => "admin_ordenes@evertec.com",
            'phone' => "+5713795628",
            'password' => bcrypt('password'),
        ]))->assignRole("Ordenes");
        User::create([
            'name' => "John Fredy Velasco Bareño",
            'email' => "jovel882@gmail.com",
            'phone' => "+573202919054",
            'password' => bcrypt('123456789'),
        ]);
    }
}
