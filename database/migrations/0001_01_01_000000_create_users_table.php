<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->string('avatar')->nullable();
        $table->decimal('daily_meal_budget', 10, 2)->default(50000);
        $table->decimal('savings_goal', 15, 2)->default(0);
        $table->string('currency', 10)->default('IDR');
        $table->string('theme', 20)->default('light');
        $table->integer('pomodoro_focus')->default(25);
        $table->integer('pomodoro_break')->default(5);
        $table->integer('daily_focus_target')->default(2);
        $table->rememberToken();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
