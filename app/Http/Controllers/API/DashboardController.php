<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskTime;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $role = strtolower($user->role);

        if ($role === 'customer') {
            return $this->dashboardCustomer($user);
        }

        return $this->dashboardEmployee($user);
    }

    private function dashboardCustomer(User $user)
    {
        $projectIds = $user->projects()->pluck('projects.id')->toArray();

        $allTasks = Task::whereIn('project_id', $projectIds)
            ->where('private', 0)
            ->with('status')
            ->select('id', 'title', 'status_id', 'created_at', 'end_date', 'private', 'project_id')
            ->get();

        $tasks = Task::where('created_by', $user->id)
            ->with('status')
            ->select('id', 'title', 'status_id', 'created_at', 'end_date', 'private')
            ->get();

        $publicTasks = $tasks->where('private', '!=', 1);

        $inProgress = $publicTasks->where('status_id', '=', 2);
        $completed = $tasks->filter(fn($t) => $t->end_date !== null);
        $finished = $completed->filter(fn($t) => $t->end_date !== null);

        $avgTime = null;
        if ($finished->count() > 0) {
            $avgTime = $finished->avg(fn($t) => strtotime($t->end_date) - strtotime($t->created_at));
        }

        return response()->json([
            'role'       => 'customer',
            'requests'   => $publicTasks->values(),
            'allTasks'   => $allTasks->values(),
            'stats' => [
                'total'     => $publicTasks->count(),
                'inProgress'=> $inProgress->count(),
                'completed' => $completed->count(),
                'avgTime'   => $avgTime ? $this->formatAvgTime($avgTime) : 'N/A',
            ],
        ]);
    }

    private function dashboardEmployee(User $user)
    {
        $projectIds = $user->projects()->pluck('projects.id')->toArray();

        $tasks = Task::whereIn('tasks.project_id', $projectIds)
            ->where('tasks.assigned_to', $user->id)
            ->select('tasks.id', 'tasks.title', 'tasks.status_id', 'tasks.private')
            ->get();

        $openTasks = $tasks->filter(fn($task) => in_array($task->status_id, [1, 2, 7]));
        $completedTasks = $tasks->where('status_id', '=', 3);

        $projectsCount = count($projectIds);

        $todayHours = TaskTime::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('hours');

        $thisWeekHours = TaskTime::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('hours');

        $thisMonthHours = TaskTime::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->sum('hours');

        $summary = TaskTime::where('user_id', $user->id)
            ->selectRaw('SUM(hours) as total_hours')
            ->first();

        return response()->json([
            'role' => 'employee',
            'tasks' => $tasks->values(),
            'openTasks' => $openTasks->values(),
            'completedTasks' => $completedTasks->values(),
            'stats' => [
                'openTasks' => $openTasks->count(),
                'completed' => $completedTasks->count(),
                'projects'  => $projectsCount,
                'totalHours'=> $summary->total_hours ?? 0,
            ],
            'timeData' => [
                'today' => $todayHours,
                'week'  => $thisWeekHours,
                'month' => $thisMonthHours,
                'total' => $summary->total_hours ?? 0,
            ],
            ]);
    }

    private function formatAvgTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        return "{$hours}h {$minutes}m";
    }
}
