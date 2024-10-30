<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller {

    // Display a list of tasks for the authenticated user
    public function index() {
        $tasks = Auth::user()->tasks; // Assumes a "tasks" relationship in the User model
        return view('tasks.index', compact('tasks'));
    }

    // Show the form for creating a new task
    public function create() {
        return view('tasks.create');
    }

    // Store a new task in the database
    public function store(Request $request) {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'status' => 'in:pending,completed,in_progress',
            'deadline' => 'nullable|date',
        ]);

        // Auth::user()->tasks()->create($request->all()); // Create task for the authenticated user

        return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    }

    // Show the form for editing the specified task
    public function edit(Task $task) {
        // Check if the authenticated user owns the task
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('tasks.edit', compact('task'));
    }

    // Update the specified task in the database
    public function update(Request $request, Task $task) {
        // Check if the authenticated user owns the task
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'status' => 'in:pending,completed,in_progress',
            'deadline' => 'nullable|date',
        ]);

        $task->update($request->all());

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }

    // Remove the specified task from the database
    public function destroy(Task $task) {
        // Check if the authenticated user owns the task
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
    }
}
