<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;





class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
public function run(): void
{
    User::create([
        'name' => 'Demo User',
        'email' => 'demo@gmail.com',
        'password' => Hash::make('password123')
    ]);
}
}
