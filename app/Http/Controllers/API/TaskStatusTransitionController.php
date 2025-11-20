<?php

namespace App\Http\Controllers\API;

use App\Models\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\TaskStatusTransition;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskStatusTransitionController extends Controller
{
    public function index()
    {
        $transitions = TaskStatusTransition::with([
            'fromStatus:id,label',
            'toStatus:id,label',
        ])
            ->orderBy('from_status_id')
            ->get();

        return response()->json($transitions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_status_id' => 'required|exists:task_statuses,id',
            'to_status_id' => 'required|exists:task_statuses,id',
        ]);

        if ($validated['from_status_id'] == $validated['to_status_id']) {
            throw ValidationException::withMessages([
                'to_status_id' => 'From a To nemôžu byť rovnaké.',
            ]);
        }

        $exists = TaskStatusTransition::where('from_status_id', $validated['from_status_id'])
            ->where('to_status_id', $validated['to_status_id'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'to_status_id' => 'Tento prechod už existuje.',
            ]);
        }

        $transition = TaskStatusTransition::create($validated);

        return response()->json($transition, 201);
    }

    public function destroy($id)
    {
        $transition = TaskStatusTransition::findOrFail($id);
        $transition->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
