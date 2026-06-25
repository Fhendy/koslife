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
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->text('description')->nullable();
        $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
        $table->enum('status', ['not_started', 'in_progress', 'completed', 'overdue'])->default('not_started');
        $table->date('deadline');
        $table->string('attachment')->nullable();
        $table->string('category')->nullable();
        $table->text('notes')->nullable();
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();
        
        $table->index(['user_id', 'status', 'deadline']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
