<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectDocumentationSection;
use Illuminate\Http\Request;

class ProjectDocumentationController extends Controller
{
    public function index(Project $project)
    {
        return $project->documentationSections()->with('user:id,name')->get();
    }

    public function store(Request $request, $projectId)
    {
        $section = ProjectDocumentationSection::create([
            'project_id' => $projectId,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json($section, 201);
    }

    public function update(Request $request, Project $project, ProjectDocumentationSection $section)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'content' => 'sometimes|string|nullable',
        ]);

        $section->update(array_merge(
            $validated,
            ['updated_by' => auth()->id()]
        ));

        return response()->json(['section' => $section]);
    }

    public function destroy(Project $project, ProjectDocumentationSection $section)
    {
        $section->delete();

        return response()->json(['message' => 'Section deleted']);
    }
}
