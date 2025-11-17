<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Helpers\ConfigHelper;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('service_hours')->get();

        $projects->transform(function($project) {
            if (!$project->service_hours) {
                $project->service_hours = (object)[
                    'hours_total' => 0,
                    'hourly_price' => 0,
                ];
            }
            return $project;
        });

        return response()->json($projects);
    }


    public function myProjects(Request $request)
    {
        $user = $request->user();
        $projects = $user->projects()->get();

        return response()->json($projects);
    }

    public function show($id)
    {
        $project = Project::with(['tasks.assigned', 'tasks.status', 'service_hours'])->findOrFail($id);

        if (!$project) {
            return response()->json(['message' => 'Projekt nebol nájdený'], 404);
        }

        return response()->json($project);
    }

    public function users($projectId)
    {
        $project = \App\Models\Project::findOrFail($projectId);

        $users = $project->users()
            ->select('users.id', 'users.name', 'users.surname')
            ->get();

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hours_total' => 'nullable|numeric|min:0',
            'hourly_price' => 'nullable|numeric|min:0',
        ]);

        $validated['created_by'] = auth()->id();

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_by' => $validated['created_by'],
        ]);

        if ($request->filled('hours_total')) {
            $project->service_hours()->create([
                'hours_total' => $validated['hours_total'],
                'hours_remaining' => $validated['hours_total'], // pri vytvarani je rovnake ako hours_total
                'hourly_price' => $validated['hourly_price'] ?? null,
            ]);
        }

        return response()->json([
            'project' => $project->load('service_hours'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hours_total' => 'nullable|numeric|min:0',
            'hourly_price' => 'nullable|numeric|min:0',
        ]);

        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['hours_total']) || isset($validated['hourly_price'])) {
            $project->service_hours()->updateOrCreate(
                ['project_id' => $project->id],
                [
                    'hours_total' => $request->filled('hours_total')
                        ? $validated['hours_total']
                        : ($project->service_hours?->hours_total ?? 0),
                    'hourly_price' => $request->filled('hourly_price')
                        ? $validated['hourly_price']
                        : ($project->service_hours?->hourly_price ?? 0),
                    'hours_remaining' => $request->filled('hours_total')
                        ? $validated['hours_total']
                        : ($project->service_hours?->hours_total ?? 0),
                ]
            );
        }

        $project->load('service_hours');

        return response()->json([
            'message' => 'Projekt bol úspešne aktualizovaný.',
            'project' => $project,
        ]);
    }


    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json([
            'message' => 'Projekt bol presunutý do archívu.',
        ]);
    }


    public function projectUsers($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);

            $allUsers = User::select('id', 'name', 'surname')->get();
            $assignedUserIds = $project->users()->pluck('users.id')->toArray();

            return response()->json([
                'allUsers' => $allUsers,
                'assignedUserIds' => $assignedUserIds,
                'allow_create_unassigned_task' => (bool) ConfigHelper::get('allow_create_unassigned_task', 1),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function updateProjectUsers(Request $request, $projectId)
    {
        $project = \App\Models\Project::findOrFail($projectId);

        $userIds = $request->input('user_ids', []);

        $project->users()->sync($userIds);

        return response()->json([
            'message' => 'Používatelia boli úspešne aktualizovaní.'
        ]);
    }
}
