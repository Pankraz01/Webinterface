<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Animation;
use App\Models\Tag;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Intervention\Image\Facades\Image;

class AnimationController extends Controller
{
    public function showUploadForm()
    {
        // Alle existierenden Tags holen
        $tags = Tag::all()->pluck('name');

        // Die Tags an das Upload-View übergeben
        return view('upload', compact('tags'));
    }

    public function upload(Request $request)
    {
        // Validierung der hochgeladenen Dateien und Tags
        // 'files.*' bedeutet, dass jede Datei in einem Array von Dateien überprüft wird
        // Es wird verlangt, dass die Dateien bestimmte Formate haben (z.B. jpg, png, mp4 etc.)
        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,mp4,mov,mxf,m4v,tif,svg,psd',
            // 'tags' muss ein Array sein, und jeder Tag muss ein String sein
            'tags' => 'required|array',
            'tags.*' => 'string',
        ]);
    
        // Iterieren durch jede hochgeladene Datei
        foreach ($request->file('files') as $index => $file) {
            // Erstellen eines eindeutigen Dateinamens mit einem Zeitstempel und dem ursprünglichen Dateinamen
            $fileName = time() . '_' . $file->getClientOriginalName();
            // Verschieben der Datei in den öffentlichen Uploads-Ordner
            $file->move(public_path('uploads'), $fileName);
    
            // Thumbnail-Erstellung wird hier übersprungen, da sie laut Beschreibung korrekt ist.
    
            // Speichern der Datei in der Datenbank in der 'animations' Tabelle
            // Dabei wird der Dateiname gespeichert, aber noch keine Tags
            $animation = Animation::create([
                'file_name' => $fileName,
            ]);
    
            // Zerlegen des Tags-Arrays, das an derselben Stelle wie die Datei steht
            // Jeder Tag wird durch Komma getrennt
            $tags = explode(',', $request->tags[$index]);
            $tagIds = [];
    
            // Iterieren durch die Tags und sie in der Datenbank speichern, falls sie noch nicht existieren
            foreach ($tags as $tagName) {
                // Entweder den existierenden Tag finden oder einen neuen erstellen
                $tag = Tag::firstOrCreate(['name' => trim($tagName)]);
                // Speichern der Tag-ID in einem Array für das spätere Zuordnen
                $tagIds[] = $tag->id;  // Nur die ID für die Pivot-Tabelle speichern
            }
    
            // Die Tags der Animation zuweisen, indem die Tag-IDs in die Pivot-Tabelle eingefügt werden
            $animation->tags()->sync($tagIds);
        }
    
        // Nach dem Hochladen und Speichern der Dateien und Tags zurück zum Dashboard leiten
        // Mit einer Erfolgsmeldung
        return redirect()->route('dashboard')->with('success', 'Dateien erfolgreich hochgeladen!');
    }
    
    
    
    
    

      

    public function download($file)
    {
        $filePath = public_path('uploads/' . $file);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            return redirect()->back()->with('error', 'Datei nicht gefunden.');
        }
    }

    public function destroy($id)
    {
        $animation = Animation::findOrFail($id);
        $filePath = public_path('uploads/' . $animation->file_name);
        $thumbnailPath = public_path('uploads/thumbnails/thumb_' . $animation->file_name);

        // Lösche die Datei, falls sie noch existiert
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Lösche das Thumbnail, falls es existiert
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }

        // Lösche den Datenbankeintrag
        $animation->delete();

        return redirect()->back()->with('success', 'Animation erfolgreich gelöscht.');
    }

    public function forgetDeleted()
    {
        $animations = Animation::all();

        foreach ($animations as $animation) {
            $filePath = public_path('uploads/' . $animation->file_name);
            $thumbnailPath = public_path('uploads/thumbnails/thumb_' . $animation->file_name);

            // Wenn die Datei nicht mehr existiert
            if (!file_exists($filePath)) {
                // Lösche das Thumbnail, falls es noch existiert
                if (file_exists($thumbnailPath)) {
                    unlink($thumbnailPath);
                }

                // Lösche den Datenbankeintrag
                $animation->delete();
            }
        }

        return redirect()->back()->with('success', 'Gelöschte Dateien erfolgreich entfernt.');
    }

    

    public function index(Request $request)
    {
        // Grundabfrage mit geladenen Tags
        $query = Animation::with('tags');
    
        // Wenn ein Tag-Filter existiert, nur Animationen mit diesem Tag laden
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->tag . '%');
            });
        }
    
        // Animationen laden
        $animations = $query->get();
    
        return view('dashboard', compact('animations'));
    }
    
}
