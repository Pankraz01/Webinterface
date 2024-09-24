<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Animation;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Intervention\Image\Facades\Image;

class AnimationController extends Controller
{
    public function showUploadForm()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,mp4,mov,mxf,m4v,tif,svg,psd',
            'tags' => 'required|array',
            'tags.*' => 'string',
        ]);
    
        foreach ($request->file('files') as $index => $file) {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);
    
            // Pfad für Thumbnails erstellen
            $thumbnailPath = public_path('uploads/thumbnails');
            if (!file_exists($thumbnailPath)) {
                mkdir($thumbnailPath, 0755, true);
            }
    
            $thumbnailName = 'thumb_' . $fileName;
            $fileExtension = $file->getClientOriginalExtension();
    
            if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                // Bild-Thumbnail erstellen
                $thumbnail = Image::make(public_path('uploads/' . $fileName))->resize(150, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $thumbnail->save($thumbnailPath . '/' . $thumbnailName);
            } elseif (in_array($fileExtension, ['mp4', 'mov', 'mxf', 'm4v'])) {
                // Video-Thumbnail mit FFmpeg erstellen
                $videoPath = public_path('uploads/' . $fileName);
                $thumbnailVideo = $thumbnailPath . '/' . $thumbnailName . '.jpg';
                $ffmpeg = "ffmpeg";
                $cmd = "$ffmpeg -i $videoPath -ss 00:00:01.000 -vframes 1 $thumbnailVideo";
                shell_exec($cmd);
            }
    
            // Speichern in der Datenbank
            Animation::create([
                'file_name' => $fileName,
                'tags' => $request->tags[$index] ?? '',
            ]);
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
        $query = Animation::query();

        if ($request->has('tag')) {
            $query->where('tags', 'like', '%' . $request->tag . '%');
        }

        $animations = $query->get();

        return view('dashboard', compact('animations'));
    }
}
