<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attachment\StoreAttachmentRequest;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Task\assignTaskRequest;
use App\Http\Requests\Task\AssignTaskRequest as TaskAssignTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateStatusTaskRequest;
use App\Models\Task;
use App\Services\AttachmentService;
use App\Services\TaskService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use PDO;

class TaskController extends Controller
{
    use ApiResponseTrait;
    protected $taskService;
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filterData = [
            'type'        => $request->input('type'),
            'status'      => $request->input('status'),
            'assigned_to' => $request->input('assigned_to'),
            'due_date'    => $request->input('due_date'),
            'priority'    => $request->input('priority'),
            'depends_on'  => $request->input('depends_on'),
            'perPage'     => $request->input('perPage')
        ];
        $tasks = $this->taskService->listTasks($filterData);
        return $this->sendResponse($tasks, "Tasks retrieved successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();
        $task = $this->taskService->createTask($validated);
        return $this->sendResponse($task, "Task created successfully", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $taskId)
    {
        $task = $this->taskService->showTask($taskId);
        return $this->sendResponse($task, "Task retrieved successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->taskService->deleteTask($id);
        return $this->sendResponse(null, "Task deleted successfully");
    }
    public function forceDelete(int $id)
    {
        $this->taskService->forceDeleteTask($id);
        return $this->sendResponse(null, "Task deleted permanently");
    }

    public function restore(int $id)
    {
        $task = $this->taskService->restoreTask($id);
        return $this->sendResponse($task, "Task restored successfully");
    }


    public function assignTask(TaskAssignTaskRequest $request, int $taskId)
    {
        $validated = $request->validated();
        $task = $this->taskService->assignTask($taskId, $validated['user_id']);
        return $this->sendResponse($task, "Task assigned successfully");
    }
    public function updateTaskStatus(UpdateStatusTaskRequest $request, $taskId)
    {
        $validated = $request->validated();
        $task = $this->taskService->updateTaskStatus($validated, $taskId);
        return $this->sendResponse($task, "Task status updated successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeFile(StoreAttachmentRequest $request, $taskId)
    {
        $validated = $request->validated();
        $file = $this->taskService->storeTaskFile($validated, $taskId);
        return $this->sendResponse($file, "File created successfully");
    }

    public function addComment(StoreCommentRequest $request, $taskId)
    {
        $validated = $request->validated();
        $comment = $this->taskService->storeTaskComment($validated, $taskId);
        return $this->sendResponse($comment, "Comment added successfully");
    }

    public function generateReportTasks(Request $request)
    {
        $filterData = [
            'type'        => $request->input('type'),
            'status'      => $request->input('status'),
            'assigned_to' => $request->input('assigned_to'),
            'due_date'    => $request->input('due_date'),
            'priority'    => $request->input('priority'),
            'depends_on'  => $request->input('depends_on'),
            'perPage'     => $request->input('perPage')
        ];
        $this->taskService->dailyReportTask($filterData);

        return $this->sendResponse(null, "Daily report generated successfully");
    }
}
