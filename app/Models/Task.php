<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'description',
        'status',
        'priority',
        'due_date',
        'assigned_to'
    ];

    protected function casts()
    {
        return [
            'type'     => TaskType::class,
            'status'   => TaskStatus::class,
            'priority' => TaskPriority::class
        ];
    }
    // status changes for this task
    public function statusUpadates()
    {
        return $this->hasMany(TaskStatusUpdate::class);
    }
    // Tasks this task depends on
    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'dependency_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class,'assigned_to');
    }
    // Tasks that depend on this task
    public function dependents()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'dependency_id', 'task_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }
    public function scopeFilterTask($query, array $inputField)
    {
        if (!empty($inputField['type'])) {
            $query->Where('type', $inputField['type']);
        }
        if (!empty($inputField['status'])) {
            $query->where('status', $inputField['status']);
        }
        if (!empty($inputField['assigned_to'])) {
            $query->where('assigned_to', $inputField['assigned_to']);
        }
        if (!empty($inputField['due_date'])) {
            $query->where('due_date', $inputField['due_date']);
        }
        if (!empty($inputField['priority'])) {
            $query->where('priority', $inputField['priority']);
        }
        if (!empty($inputField['depends_on'])) {
            $query->whereRelation('dependencies', 'dependency_id', $inputField['depends_on']);
        }
    }
}
