<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\Task;
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
            $table->string('title');
            $table->text('description');
            $table->enum('type', [
                TaskType::BUG->value,
                TaskType::FEATURE->value,
                TaskType::IMPOVEMENT->value
            ]);
            $table->enum('status', [
                TaskStatus::OPEN->value,
                TaskStatus::IN_PROGRESS->value,
                TaskStatus::COMPLETED->value,
                TaskStatus::BLOCKED->value
            ]);
            $table->enum('priority', [
                TaskPriority::LOW->value,
                TaskPriority::MEDIUM->value,
                TaskPriority::HIGH->value
            ]);
            $table->date('due_date');
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
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
