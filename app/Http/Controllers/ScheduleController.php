<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScheduleController extends Controller
{
    public function view(Schedule $schedule)
    {
        // ✅ Enregistrer la vue (unique par user)
        $schedule->registerView();
        
        if ($schedule->isPdf()) {
            return view('schedules.pdf-viewer', compact('schedule'));
        } else {
            return view('schedules.image-viewer', compact('schedule'));
        }
    }

    public function serve(Schedule $schedule)
    {
        $path = storage_path('app/public/' . $schedule->file_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function download(Schedule $schedule)
    {
        $schedule->incrementDownloadCount();

        $path = storage_path('app/public/' . $schedule->file_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $schedule->title . '.' . $schedule->extension);
    }
}