<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaskComment;
use App\Services\FileUpload;

class TaskAttachmentController extends Controller
{
    public function store(Request $request, $taskId, $commentId)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240',
        ]);

        $comment = TaskComment::findOrFail($commentId);

        $attachmentsWithUrl = [];

        foreach ($request->file('files', []) as $file) {

            $fileData = FileUpload::uploadAttachment($file);

            $attachment = $comment->attachments()->create([
                'filename' => $fileData['filename'],
                'path' => $fileData['path'],
                'uploaded_by' => auth()->id(),
            ]);

            $attachmentsWithUrl[] = [
                'id' => $attachment->id,
                'filename' => $attachment->filename,
                'url' => $fileData['url'],
            ];
        }

        return response()->json([
            'message' => 'Súbory nahrané úspešne.',
            'attachments' => $attachmentsWithUrl
        ]);
    }
}
