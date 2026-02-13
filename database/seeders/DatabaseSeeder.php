<?php

namespace Database\Seeders;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders. */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Vincent Bakker',
            'email' => 'test@gmail.com',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        


        DB::table('articles')->insert([
    [
        'name' => 'First test article',
        'content' => 'This is test content for article one.',
        'summary' => 'Project summary in short',
        'created_at' => now(),
        'updated_at' => now(),
        'status' => false,
        'visibility' => true,
        
    ],
    [
        'name' => 'Second test article',
        'content' => 'This is test content for article one.',
        'summary' => 'Project summary in short',
        'created_at' => now(),
        'updated_at' => now(),
        'status' => false,
        'visibility' => true,
        
    ],
]);
}
}