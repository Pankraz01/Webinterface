<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Animation;

class AnimationController extends Controller
{
    public function showUploadForm()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,mp4', // Passe die Dateitypen nach Bedarf an
        ]);
    
        foreach ($request->file('files') as $file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);
            // Hier kannst du auch die Dateiinformationen in der Datenbank speichern
        }
    
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
    
        // Lösche die Datei, falls sie noch existiert
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    
        // Lösche den Datenbankeintrag
        $animation->delete();
    
        return redirect()->back()->with('success', 'Animation erfolgreich gelöscht.');
    }
    
    public function forgetDeleted()
    {
        $animations = Animation::all();
    
        foreach ($animations as $animation) {
            if (!file_exists(public_path('uploads/' . $animation->file_name))) {
                $animation->delete();
            }
        }
    
        return redirect()->back()->with('success', 'Gelöschte Dateien erfolgreich entfernt.');
    }
    

    public function index(Request $request)
    {
        $query = Animation::query();

        if ($request->has('tag')) {
            $query->where('tags', 'like', '%' . $request->tag . '%');
        }

        $animations = $query->get();

        return view('dashboard', compact('animations'));
    }
}
