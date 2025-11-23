<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskTime;
use App\Models\TaskStatus;
use Illuminate\Support\Facades\DB;
use App\Helpers\ConfigHelper;
use App\Services\IcsExport;
use App\Models\TaskEditLog;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $userProjectIds = DB::table('project_users')
            ->where('user_id', $user->id)
            ->pluck('project_id')
            ->toArray();

        if (empty($userProjectIds)) {
            return response()->json([]);
        }

        $query = Task::with(['project', 'assigned', 'status'])
            ->whereIn('project_id', $userProjectIds)
            ->select('id', 'title', 'status_id', 'project_id', 'assigned_to', 'private', 'created_at', 'due_date');

        if ($request->filled('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('name', $request->status);
            });
        }

        if ($request->filled('project')) {
            $query->where('project_id', $request->project);
        }

        if ($request->filled('assigned')) {
            $query->where('assigned_to', $request->assigned);
        }

        $perPage = $request->input('perPage', 20);
        $tasks = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($tasks);
    }

    public function show($id)
    {
        $task = Task::with(['assigned', 'project', 'status'])->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $user = auth()->user();
        $isCustomer = $user->role === 'customer';

        if ($task->private && $isCustomer) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $workedHours = TaskTime::where('task_id', $task->id)->sum('hours');

        $data = [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'project_id' => $task->project?->id,
            'status' => [
                'id' => $task->status->id ?? null,
                'name' => $task->status->name ?? null,
                'label' => $task->status->label ?? null,
                'color_bg' => $task->status->color_bg ?? null,
                'color_text' => $task->status->color_text ?? null,
            ],
            'project' => $task->project ? [
                'id' => $task->project->id,
                'name' => $task->project->name
            ] : null,
            'assigned' => $task->assigned ? [
                'id' => $task->assigned->id,
                'name' => $task->assigned->name,
                'surname' => $task->assigned->surname
            ] : null,
            'created_at' => $task->created_at,
            'due_date' => $task->due_date ?? null,
            'worked_hours' => (float) $workedHours,
            'private' => (bool) $task->private ?? null,
        ];

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $allowUnassigned = ConfigHelper::get('allow_create_unassigned_task', 1); // default je true

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'due_date' => 'nullable|date',
            'assigned_to' => $allowUnassigned
                ? 'nullable|exists:users,id'
                : 'required|exists:users,id',
            'private' => 'nullable|boolean',
        ], [
            'assigned_to.required' => 'Nová úloha musí byť priradená používateľovi.',
        ]);
        $statusId = TaskStatus::where('name', 'priradena')->value('id');

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status_id' => $statusId,
            'due_date' => $validated['due_date'] ?? null,
            'project_id' => $validated['project_id'],
            'assigned_to' => $validated['assigned_to'] ?? null,
            'private' => $validated['private'] ?? 0,
            'created_by' => auth()->id(),
        ]);

        TaskEditLog::create([
            'task_id'   => $task->id,
            'user_id'   => auth()->id(),
            'type'      => TaskEditLog::TYPE_TASK_CREATED,
            'old_value' => null,
            'new_value' => json_encode([
                'title'       => $task->title,
            ]),
            'notified' => 0,
        ]);

        $task->load(['assigned', 'project', 'status']);

        return response()->json($task, 201);
    }


    public function exportIcs(IcsExport $ics)
    {
        $content = $ics->generateForTasks();

        return response($content, 200)
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'attachment; filename="tasks.ics"');
    }

    public function myActiveTasks(Request $request)
    {
        $user = $request->user();

        $tasks = Task::with(['project', 'assigned', 'status'])
            ->whereNotIn('status_id', [3, 4, 8])
            ->where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhereNull('assigned_to');
            })
            ->orderByDesc('created_at')
            ->get();

        $tasks = $tasks->map(function($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'project' => [
                    'id' => $task->project?->id,
                    'name' => $task->project?->name ?? 'Bez projektu',
                ],
                'assigned' => [
                    'id' => $task->assigned?->id,
                    'name' => $task->assigned?->name ?? 'Nepriradené',
                    'surname' => $task->assigned?->surname ?? '',
                ],
                'due_date' => $task->due_date?->format('Y-m-d') ?? null,
                'status' => [
                    'id' => $task->status?->id,
                    'name' => $task->status?->name ?? 'Neznámy',
                    'label' => $task->status?->label ?? ucfirst($task->status?->name ?? 'Neznámy'),
                ],
            ];
        });

        return response()->json($tasks);
    }

    public function myCompletedTasks(Request $request)
    {
        $tasks = Task::where('assigned_to', auth()->id())
            ->where('status_id', 3,)
            ->get();

        return response()->json($tasks);
    }

    public function myRequests(Request $request)
    {
        $user = $request->user();

        $tasks = Task::with(['project', 'assigned', 'status'])
            ->where('created_by', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $tasks = $tasks->map(function($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'project' => [
                    'id' => $task->project?->id,
                    'name' => $task->project?->name ?? 'Bez projektu',
                ],
                'assigned' => [
                    'id' => $task->assigned?->id,
                    'name' => $task->assigned?->name ?? 'Nepriradené',
                    'surname' => $task->assigned?->surname ?? '',
                ],
                'status' => [
                    'id' => $task->status?->id,
                    'name' => $task->status?->name ?? 'Neznámy',
                    'label' => $task->status?->label ?? ucfirst($task->status?->name ?? 'Neznámy'),
                    'color_bg' => $task->status?->color_bg ?? 'bg-gray-100',
                    'color_text' => $task->status?->color_text ?? 'text-gray-700',
                ],
                'created_at' => $task->created_at,
                'due_date' => $task->due_date?->format('Y-m-d') ?? null,
            ];
        });

        return response()->json($tasks);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'due_date' => 'sometimes|nullable|date',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
        ]);

        $user = $request->user();

        $oldValues = $task->only(['assigned_to', 'due_date']);

        $task->update($validated);

        foreach (['assigned_to', 'due_date'] as $key) {
            if (array_key_exists($key, $validated)) {
                $oldValue = $oldValues[$key] ?? null;
                $newValue = $validated[$key];

                if ($oldValue != $newValue) {
                    $type = $key === 'assigned_to'
                        ? TaskEditLog::TYPE_ASSIGNED_USER
                        : TaskEditLog::TYPE_DUE_DATE;

                    if ($key === 'due_date') {
                        $oldValue = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('Y-m-d') : null;
                        $newValue = $newValue ? \Carbon\Carbon::parse($newValue)->format('Y-m-d') : null;
                    }

                    TaskEditLog::create([
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                        'type' => $type,
                        'old_value' => (string) $oldValue,
                        'new_value' => (string) $newValue,
                        'notified' => false,
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Úloha bola úspešne aktualizovaná.',
            'task' => $task
        ]);
    }

    public function inProgress(Request $request)
    {
        $user = $request->user();

        $projectIds = $user->projects()->pluck('projects.id');

        $tasks = Task::with(['status', 'project'])
            ->whereIn('project_id', $projectIds)
            ->where('status_id', 2)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($tasks);
    }

    // musi byt dalsia metoda, pretoze myCompletedTasks() vracia iba dokončené ulohy ktoré vytvoril pouzivatel
    // completed() vracia vsetky dokončené ulohy v projekte
    public function completed(Request $request)
    {
        $user = $request->user();

        $projectIds = $user->projects()->pluck('projects.id');

        $tasks = Task::with(['status', 'project'])
            ->whereIn('project_id', $projectIds)
            ->where('status_id', 3)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($tasks);
    }
}
