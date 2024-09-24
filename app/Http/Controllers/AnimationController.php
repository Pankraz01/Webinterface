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
            'file' => 'required|file',
            'tags' => 'required|string',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $fileName);

        Animation::create([
            'file_name' => $fileName,
            'tags' => $request->tags,
        ]);

        return redirect()->back()->with('success', 'Animation erfolgreich hochgeladen!');
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
