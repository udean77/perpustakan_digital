<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        user::create([
            'nama'=>'Administrator',
            'email'=>'Admin@gmail.com',
            'role'=>'admin',
            'status'=>'active',
            'password'=> bcrypt('abcd1234'),
            'hp'=> '0895613297075',
        ]);
        user::create([
            'nama'=>'Ilham',
            'email'=>'ilham@gmail.com',
            'role'=>'pembeli',
            'status'=>'active',
            'password'=> bcrypt('P@55word'),
            'hp'=> '0895613297075',
        ]);
       
        $this->call([
            // RegionsSeeder::class, // Temporarily disabled
            RedeemCodeSeeder::class,
            TestDataSeeder::class,
            BookSeeder::class,
        ]);
    }
}
