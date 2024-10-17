<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TaskStatusUpdate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_id',
        'previous_status',
        'new_status'
    ];

    protected function casts()
    {
        return [
            'previous_status' => TaskStatus::class,
            'new_status'      => TaskStatus::class
        ];
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
