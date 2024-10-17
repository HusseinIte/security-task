<?php

use App\Enums\TaskStatus;
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
        Schema::create('task_status_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->enum('previous_status', [
                TaskStatus::OPEN->value,
                TaskStatus::IN_PROGRESS->value,
                TaskStatus::COMPLETED->value,
                TaskStatus::BLOCKED->value
            ]);
            $table->enum('new_status', [
                TaskStatus::OPEN->value,
                TaskStatus::IN_PROGRESS->value,
                TaskStatus::COMPLETED->value,
                TaskStatus::BLOCKED->value
            ]);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_status_updates');
    }
};
