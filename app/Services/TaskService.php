<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Exceptions\CustomException;
use App\Exceptions\NotFoundException;
use App\Models\Task;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * TaskService handles task-related operations including task creation, retrieval, update, deletion, and more.
 */

class TaskService
{
    /**
     * @var AttachmentService $attachmentService The service to manage attachments.
     */
    protected $attachmentService;

    /**
     * TaskService constructor.
     *
     * @param AttachmentService $attachmentService
     */
    public function __construct(
        AttachmentService $attachmentService
    ) {
        $this->attachmentService = $attachmentService;
    }

    /**
     * List tasks based on provided filter data, cached for 60 minutes.
     *
     * @param array $filterData Filter parameters for task query.
     * @return \Illuminate\Support\Collection List of tasks.
     * @throws CustomException If an error occurs during task retrieval.
     */
    public function listTasks(array $filterData)
    {
        try {
            // Generate a unique cache key based on filter parameters
            $cacheKey = 'filtered_tasks_' . implode('_', array_filter($filterData));
            // Cache the query result for 60 minutes
            return Cache::remember($cacheKey, 60, function () use ($filterData) {
                $tasks = Task::filterTask($filterData)->get();
                return $tasks;
            });
        } catch (Exception $e) {
            Log::error("An unexpected error while fetching tasks. " . $e->getMessage());
            throw new CustomException("An unexpected error while fetching tasks.");
        }
    }

    /**
     * Create a new task with the given data.
     *
     * @param array $data Data for task creation.
     * @return Task Newly created task.
     * @throws CustomException If an error occurs during task creation.
     */
    public function createTask(array $data)
    {
        try {
            // Check if all dependencies are completed if dependency_ids are provided
            $status = $this->getTaskStatus($data['dependency_ids'] ?? []);
            $task = Task::create([
                'title'       => $data['title'],
                'type'        => $data['type'],
                'description' => $data['description'],
                'status'      => $status,
                'priority'    => $data['priority'],
                'due_date'    => $data['due_date'],
                'assigned_to' => $data['assigned_to']
            ]);
            if (!empty($data['dependency_ids'])) {
                $task->dependencies()->attach($data['dependency_ids']);
            }
            return $task;
        } catch (QueryException $e) {
            Log::error("Database query error while creating task. " . $e->getMessage());
            throw new CustomException("Database query error while creating task.", 500);
        } catch (Exception $e) {
            Log::error("An unexpected error while creating task. " . $e->getMessage());
            throw new CustomException("An unexpected error while creating task.", 500);
        }
    }

    /**
     * Retrieve a specific task by its ID.
     *
     * @param int $taskId The ID of the task.
     * @return Task The retrieved task.
     * @throws NotFoundException If the task is not found.
     * @throws CustomException If an unexpected error occurs during task retrieval.
     */
    public function showTask(int $taskId)
    {
        try {
            $task = Task::findOrFail($taskId);
            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error("Task id $taskId not found for retrieve." . $e->getMessage());
            throw new NotFoundException("Task Not Found.");
        } catch (Exception $e) {
            Log::error("An unexpected error while retrieving task. " . $e->getMessage());
            throw new CustomException("An unexpected error while retrieving task.", 500);
        }
    }


    /**
     * Soft delete a task by its ID.
     *
     * @param int $taskId The ID of the task.
     * @return void
     * @throws NotFoundException If the task is not found.
     * @throws CustomException If an unexpected error occurs during task deletion.
     */
    public function deleteTask(int $taskId)
    {
        try {
            $task = Task::findOrFail($taskId);
            $task->delete();
        } catch (ModelNotFoundException $e) {
            Log::error("Task id $taskId not found for soft delete." . $e->getMessage());
            throw new NotFoundException("Task Not Found.");
        } catch (Exception $e) {
            Log::error("An unexpected error while soft deleting task. " . $e->getMessage());
            throw new CustomException("An unexpected error while soft deleting task.", 500);
        }
    }
    /**
     * Permanently delete a task by its ID.
     *
     * @param int $taskId The ID of the task.
     * @return void
     * @throws NotFoundException If the task is not found.
     * @throws CustomException If an unexpected error occurs during task deletion.
     */
    public function forceDeleteTask(int $taskId)
    {
        try {
            $task = Task::onlyTrashed()->findOrFail($taskId);
            $task->forceDelete();
        } catch (ModelNotFoundException $e) {
            Log::error("Task id $taskId not found for delete." . $e->getMessage());
            throw new NotFoundException("Task Not Found.");
        } catch (Exception $e) {
            Log::error("An unexpected error while  deleting task. " . $e->getMessage());
            throw new CustomException("An unexpected error while  deleting task.", 500);
        }
    }

    /**
     * Restore a soft-deleted task by its ID.
     *
     * @param int $taskId The ID of the task.
     * @return Task The restored task.
     * @throws NotFoundException If the task is not found.
     * @throws CustomException If an unexpected error occurs during task restoration.
     */
    public function restoreTask(int $taskId)
    {
        try {
            $task = Task::onlyTrashed()->findOrFail($taskId);
            $task->restore();
            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error("Task id $taskId not found for restore." . $e->getMessage());
            throw new NotFoundException("Task Not Found.");
        } catch (Exception $e) {
            Log::error("An unexpected error while  restoring task. " . $e->getMessage());
            throw new CustomException("An unexpected error while  restoring task.", 500);
        }
    }
    /**
     * Update the status of a task.
     *
     * @param array $data Data for task status update.
     * @param int $taskId The ID of the task.
     * @return Task Updated task.
     * @throws NotFoundException If the task is not found.
     * @throws CustomException If an unexpected error occurs during status update.
     */
    public function updateTaskStatus(array $data, $taskId)
    {
        try {
            $task = Task::findOrFail($taskId);
            $previouse_status = $task->status;
            $task->status = $data['status'];
            $task->save();
            $this->createTaskStatusUpdate($task, $previouse_status);
            // Check if the status is complete and then update all tasks status dependent on this task
            if ($task->status === TaskStatus::COMPLETED) {
                $this->updateDependentsTaskStatus($task);
            }
            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error("Task id $taskId not found for update status." . $e->getMessage());
            throw new NotFoundException("Task Not Found.");
        } catch (Exception $e) {
            Log::error("An unexpected error while update status task. " . $e->getMessage());
            throw new CustomException("An unexpected error while update status task.", 500);
        }
    }

    /**
     * Assign a task to a user.
     *
     * @param int $taskId The ID of the task.
     * @param int $assigned_to ID of the user to assign the task to.
     * @return Task Updated task with the new assignment.
     * @throws NotFoundException If the task is not found.
     * @throws CustomException If an unexpected error occurs during task assignment.
     */
    public function assignTask(int $taskId, int $assigned_to)
    {
        try {
            $task = Task::findOrFail($taskId);
            $task->assigned_to = $assigned_to;
            $task->save();
            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error("Task id $taskId not found for assign to user." . $e->getMessage());
            throw new NotFoundException("Task Not Found.");
        } catch (Exception $e) {
            Log::error("An unexpected error while assign task to user. " . $e->getMessage());
            throw new CustomException("An unexpected error while assign task to user. ", 500);
        }
    }


    /**
     * Store a file attachment for a task.
     *
     * @param array $data Data for the file attachment.
     * @param int $taskId The ID of the task.
     * @return \Illuminate\Database\Eloquent\Model Newly created attachment.
     * @throws NotFoundException If the task is not found.
     * @throws CustomException If an error occurs during file upload.
     */
    public function storeTaskFile(array $data, $taskId)
    {
        try {
            $file = $this->attachmentService->uploadFile($data, $taskId);
            $task = Task::findOrFail($taskId);
            return $task->attachments()->create([
                'path'      =>  $file->filePath,
                'name'      =>  $file->name,
                'mime_type' => $file->mime_type,
                'alt_text'  => $file->alt_text
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error("Task id $taskId not found." . $e->getMessage());
            throw new NotFoundException("Task Not Found.");
        } catch (Exception $e) {
            Log::error("An unexpected error while upload file for task. " . $e->getMessage());
            throw new CustomException("An unexpected error while upload file for task.", 500);
        }
    }

    /**
     * Store a comment on a task.
     *
     * @param array $data Data for the comment.
     * @param int $taskId The ID of the task.
     * @return \Illuminate\Database\Eloquent\Model Newly created comment.
     * @throws NotFoundException If the task is not found.
     * @throws CustomException If an error occurs while adding the comment.
     */
    public function storeTaskComment(array $data, $taskId)
    {
        try {
            $task = Task::findOrFail($taskId);
            return $task->comments()->create([
                'comment' => $data['comment']
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error("Task id $taskId not found for add comment." . $e->getMessage());
            throw new NotFoundException("Task Not Found.");
        } catch (Exception $e) {
            Log::error("An unexpected error while add comment to task. " . $e->getMessage());
            throw new CustomException("An unexpected error while add comment to task.", 500);
        }
    }

    /**
     * Determine the task status based on the completion status of its dependencies.
     *
     * @param array $dependency_ids Array of dependency task IDs.
     * @return string The calculated task status.
     */
    private function getTaskStatus(array $dependency_ids)
    {
        if (!empty($dependency_ids) && !$this->checkIfAllDependenciesCompleted($dependency_ids)) {
            return TaskStatus::BLOCKED;
        }
        return TaskStatus::OPEN;
    }

    /**
     * Check if all dependencies of a task are completed.
     *
     * @param array $dependency_ids Array of dependency task IDs.
     * @return bool True if all dependencies are completed, false otherwise.
     */
    public function checkIfAllDependenciesCompleted(array $dependency_ids)
    {
        // Check if there are any incomplete dependencies
        return !Task::whereIn('id', $dependency_ids)
            ->where('status', '!=', TaskStatus::COMPLETED)
            ->exists();
    }

    /**
     * Update the status of tasks that are dependent on the given task.
     *
     * @param Task $task The task whose dependents' statuses need to be updated.
     * @return void
     */
    public function updateDependentsTaskStatus(Task $task)
    {
        $dependents = $task->dependents;
        foreach ($dependents as $dependent) {
            $dependency_ids = $dependent->dependencies->pluck('id')->toArray();
            $previouse_status = $dependent->status;
            $dependent->status = $this->getTaskStatus($dependency_ids);
            $dependent->save();
            if ($previouse_status !== $dependent->status) {
                // register task status changes in task_status_updates table
                $this->createTaskStatusUpdate($dependent, $previouse_status);
            }
        }
    }

    /**
     * Create a record of task status updates.
     *
     * @param Task $task The task that had its status updated.
     * @param string $previouse_status The previous status of the task.
     * @return void
     */
    public function createTaskStatusUpdate(Task $task, $previouse_status)
    {
        $task->statusUpadates()->create([
            'previous_status' => $previouse_status,
            'new_status'      => $task->status
        ]);
    }


    /**
     * Generate a daily report for tasks based on the provided filter data.
     *
     * @param array $filterData Filter parameters for task query.
     * @return bool True if the report is successfully generated and saved, false otherwise.
     */

    public function dailyReportTask(array $filterData)
    {
        $date = now()->format('Y-m-d');
        $tasks = Task::filterTask($filterData)->whereDate('created_at', Carbon::today())->get();
        // generate pdf report
        $pdf = Pdf::loadView('reports/daily-reports', ['tasks' => $tasks, 'date' => $date]);
        // save report in folder reports with name depend on filter data in public folder
        $filter = !empty(array_filter($filterData)) ? implode('_', array_filter($filterData)) : "general";
        $filePath = 'reports/' . $filter . '/daily-report-' . $date . '.pdf';
        return Storage::disk('public')->put($filePath, $pdf->output());
    }
}
