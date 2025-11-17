<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaskComment;
use App\Models\TaskAttachment;

class TaskCommentController extends Controller
{
    public function index($taskId)
    {
        $comments = TaskComment::with('user', 'attachments')
            ->where('task_id', $taskId)
            ->orderBy('created_at', 'asc')
            ->get();

        $comments->each(function ($comment) {
            $comment->attachments->each(function ($att) {
                $att->url = $att->url;
            });
        });

        return response()->json($comments);
    }

    public function store(Request $request, $taskId)
    {
        $request->validate([
            'text' => 'required|string|max:2000',
        ]);

        $comment = TaskComment::create([
            'task_id' => $taskId,
            'user_id' => $request->user()->id,
            'text' => $request->text,
        ]);

        $comment->load('user', 'attachments');

        return response()->json($comment, 201);
    }
}
