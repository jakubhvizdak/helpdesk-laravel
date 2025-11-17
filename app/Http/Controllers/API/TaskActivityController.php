<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaskActivity;

class TaskActivityController extends Controller
{
    public function index()
    {
        return response()->json(
            TaskActivity::select('id', 'name', 'label')->orderBy('label')->get()
        );
    }
}
