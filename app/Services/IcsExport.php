<?php

namespace App\Services;

use App\Models\Task;

class IcsExport
{
    public function generateForTasks()
    {
        $tasks = Task::with('status')->get();

        $ics = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//HelpdeskSystem//EN\r\n";

        foreach ($tasks as $task) {
            $start = date('Ymd', strtotime($task->created_at));
            $end = date('Ymd', strtotime($task->due_date ?? $task->created_at));

            $ics .= "BEGIN:VEVENT\r\n";
            $ics .= "UID:task-{$task->id}@helpdesk.local\r\n";
            $ics .= "DTSTAMP:" . date('Ymd\THis\Z') . "\r\n";
            $ics .= "DTSTART;VALUE=DATE:{$start}\r\n";
            $ics .= "DTEND;VALUE=DATE:{$end}\r\n";
            $ics .= "SUMMARY:" . addslashes($task->title) . " ({$task->status->label})\r\n";
            $ics .= "DESCRIPTION:" . addslashes($task->description ?? '') . "\r\n";
            $ics .= "END:VEVENT\r\n";
        }

        $ics .= "END:VCALENDAR\r\n";

        return $ics;
    }
}
