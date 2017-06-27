<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\User')->create([
            'email' => 'paul@example.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
