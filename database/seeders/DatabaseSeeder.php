<?php
namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Project;
use App\Models\Workspace;
use App\Models\Category;
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders. 
     */
    public function run(): void
    { 
        
        /** 
         * Run the database factories
        */
        /* ------------------------------------ */
    

        

        DB::table('users')->insert([
            'name' => 'Admin',
            'role' => 'admin',
            'email' => 'Admin@gmail.com',
            'address' => 'Kyano',
            'company' => 'Kyano Digital',
            'phone_number' => '053853833',
            'password' => Hash::make('Admin@password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('categories')->insert([
            'name' => 'Tech',
            'slug' => 'tech',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        Project::factory()
        ->count(6)
        ->create();
        
        Article::factory()
        ->count(9)
        ->create();
        
         DB::table('tags')->insert([
            [
                'name' => 'personal',
                'created_at' => now(),
                'updated_at' => now(),
                
            ],
            [
                'name' => 'php',
                'created_at' => now(),
                'updated_at' => now(),
                ],
        ]);



    }
}