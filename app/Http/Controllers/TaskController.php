<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::with('project');
        
        // Filter by project if specified
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        $tasks = $query->get();
        return response()->json($tasks);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:draft,in_progress,done',
            'project_id' => 'required|exists:projects,id',
            'weight' => 'required|integer|min:1'
        ]);
        
        $task = Task::create($validated);
        $task->load('project');
        
        // Update project progress
        $task->project->updateProgress();
        
        return response()->json([
            'success' => true,
            'message' => 'Task berhasil dibuat',
            'data' => $task
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        $task->load('project');
        return response()->json($task);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:draft,in_progress,done',
            'project_id' => 'required|exists:projects,id',
            'weight' => 'required|integer|min:1'
        ]);
        
        $task->update($validated);
        $task->load('project');
        
        // Update project progress
        $task->project->updateProgress();
        
        return response()->json([
            'success' => true,
            'message' => 'Task berhasil diupdate',
            'data' => $task
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $project = $task->project;
        $task->delete();
        
        // Update project progress after task deletion
        $project->updateProgress();
        
        return response()->json([
            'success' => true,
            'message' => 'Task berhasil dihapus'
        ]);
    }
}
