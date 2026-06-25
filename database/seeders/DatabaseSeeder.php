<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Habit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo user
        $user = User::create([
            'name' => 'Anak Kos',
            'email' => 'kos@example.com',
            'password' => Hash::make('password'),
            'daily_meal_budget' => 50000,
            'savings_goal' => 1000000,
            'theme' => 'light',
            'pomodoro_focus' => 25,
            'pomodoro_break' => 5,
            'daily_focus_target' => 2
        ]);
        
        // Create default habits
        $habits = [
            ['name' => 'Bangun Pagi', 'icon' => '🌅'],
            ['name' => 'Belajar', 'icon' => '📚'],
            ['name' => 'Ngoding', 'icon' => '💻'],
            ['name' => 'Membaca', 'icon' => '📖'],
            ['name' => 'Olahraga', 'icon' => '🏋️'],
            ['name' => 'Minum Air', 'icon' => '💧'],
            ['name' => 'Tidur Tepat Waktu', 'icon' => '😴'],
        ];
        
        foreach ($habits as $habit) {
            Habit::create([
                'user_id' => $user->id,
                'name' => $habit['name'],
                'icon' => $habit['icon'],
                'target_frequency' => 'daily',
                'is_active' => true
            ]);
        }
    }
}