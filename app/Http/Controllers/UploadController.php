<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessUpload;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadWithWorker(Request $request)
    {
        // Validierung
        $request->validate([
            'file' => 'required|file',
        ]);

        // Datei-Infos
        $file = $request->file('file');
        $filePath = $file->store('temp');  // Datei temporär speichern

        // Job starten
        ProcessUpload::dispatch($filePath);
        Log::info('Job dispatched: ' . $filePath);

        return response()->json(['status' => 'Datei im Hintergrund verarbeitet'], 200);
    }
}
