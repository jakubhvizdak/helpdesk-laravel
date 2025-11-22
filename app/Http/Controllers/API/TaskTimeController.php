<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaskTime;
use App\Models\TaskActivity;
use Carbon\Carbon;
use App\Models\Task;
use App\Helpers\ConfigHelper;


class TaskTimeController extends Controller
{

    public function getAll(Request $request)
    {
        $userId = $request->user()->id;

        $times = TaskTime::with([
            'user:id,name,surname',
            'task:id,title,project_id,private',
            'task.project.users',
            'activity:id,name,label',
        ])
            ->whereHas('task.project', function ($projectQuery) use ($userId) {
                $projectQuery->whereHas('users', function ($userQuery) use ($userId) {
                    $userQuery->where('users.id', $userId);
                });
            })
            ->orderByDesc('worked_at')
            ->get();

        return response()->json($times);
    }

    public function index($taskId)
    {
        $times = TaskTime::with(['user:id,name', 'activity:id,name,label'])
            ->where('task_id', $taskId)
            ->orderBy('worked_at', 'asc')
            ->get();

        return response()->json($times);
    }

    public function store(Request $request, $taskId)
    {
        $request->validate([
            'hours' => 'required|numeric|min:0.25',
            'activity_id' => 'required|exists:task_activities,id',
            'description' => 'nullable|string|max:2000',
            'worked_at' => 'nullable|date',
        ]);

        $time = TaskTime::create([
            'task_id' => $taskId,
            'user_id' => $request->user()->id,
            'hours' => $request->hours,
            'activity_id' => $request->activity_id,
            'description' => $request->description,
            'worked_at' => $request->worked_at ?? now(),
        ]);

        $configActivityId = ConfigHelper::get('service_hours_deduction_activity_id');

        if ($configActivityId && (int)$configActivityId === (int)$request->activity_id) {
            $task = Task::with('project.service_hours')->find($taskId);

            if ($task && $task->project && $task->project->service_hours) {
                $serviceHours = $task->project->service_hours;

                $serviceHours->hours_remaining = max(0, $serviceHours->hours_remaining - $request->hours);
                $serviceHours->save();

                $time->service_hour_id = $serviceHours->id;
                $time->save();
            }
        }

        $time->load(['user:id,name', 'activity:id,name,label']);

        return response()->json($time, 201);
    }

    public function getSummary(Request $request)
    {
        $userId = $request->user()->id;

        $times = TaskTime::whereHas('task.project.users', function($q) use ($userId) {
            $q->where('users.id', $userId);
        })->get();

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $totalHours = $times->sum('hours');

        $hours = [
            'today' => $times->where('worked_at', '>=', $today)->sum('hours'),
            'week' => $times->where('worked_at', '>=', $startOfWeek)->sum('hours'),
            'month' => $times->where('worked_at', '>=', $startOfMonth)->sum('hours'),
            'total_hours' => $totalHours,
        ];

        return response()->json($hours);
    }
}
