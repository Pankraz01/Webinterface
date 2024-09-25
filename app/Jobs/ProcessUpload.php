<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessFileUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle()
    {
        Log::info('Job erfolgreich gestartet für Datei: ' . $fileName);

        // Verschiebe die Datei vom temporären Pfad zum Zielordner
        $tempPath = storage_path('app/' . $this->filePath);
        $destinationPath = public_path('uploads/' . basename($this->filePath));

        // Datei verschieben
        if (file_exists($tempPath)) {
            rename($tempPath, $destinationPath);
        }
        Log::info('Job erfolgreich beendet für Datei: ' . $fileName);

    }
}
