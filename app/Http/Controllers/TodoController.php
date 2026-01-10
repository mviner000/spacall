<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Events\TodoCreated;
use App\Events\TodoUpdated;
use App\Events\TodoDeleted;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index()
    {
        return response()->json(Todo::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'completed' => 'boolean'
        ]);

        $todo = Todo::create($validated);
        
        // Broadcast the event
        event(new TodoCreated($todo));
        
        return response()->json($todo, 201);
    }

    public function show(Todo $todo)
    {
        return response()->json($todo);
    }

    public function update(Request $request, Todo $todo)
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'completed' => 'boolean'
        ]);

        $todo->update($validated);
        
        // Broadcast the event
        event(new TodoUpdated($todo));
        
        return response()->json($todo);
    }

    public function destroy(Todo $todo)
    {
        $todoId = $todo->id;
        $todo->delete();
        
        // Broadcast the event
        event(new TodoDeleted($todoId));
        
        return response()->json(null, 204);
    }
}
