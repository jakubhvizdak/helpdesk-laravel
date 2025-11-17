<?php

namespace App\Services;

class FileUpload
{
    public static function uploadAttachment($file, $folder = 'task_attachments')
    {
        $filename = self::uniqueName($file);
        $path = $file->storeAs($folder, $filename, 'public');

        return [
            'filename' => $file->getClientOriginalName(),
            'stored_name' => $filename,
            'path' => $path,
            'url' => asset('storage/' . $path),
        ];
    }

    private static function uniqueName($file)
    {
        $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();

        return $original . '_' . time() . '.' . $ext;
    }
}
