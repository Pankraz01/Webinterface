@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Animationsliste</h2>

    <div class="container">
        <!-- Filter-Formular -->
        <form method="GET" action="{{ route('dashboard') }}">
            <div class="input-group mb-3">
                <input type="text" name="tag" class="form-control" placeholder="z.B. transition, ink, monochrom" aria-label="Tag">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">Filtern</button>
                </div>
            </div>
        </form>

        <!-- Buttons unter dem Filter -->
        <div class="row">
            <div class="col-md-4 mb-2">
                <form action="{{ route('animations.forgetDeleted') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-block">
                        <i class="fas fa-trash"></i> Forget Deleted Files
                    </button>
                </form>
            </div>
            <div class="col-md-4 mb-2">
                <a href="{{ route('animations.upload') }}" class="btn btn-primary btn-block">
                    <i class="fas fa-upload"></i> Animation hochladen
                </a>
            </div>
        </div>
    </div>

    <!-- Datei-Tabelle -->
    <table class="table mt-4">
        <thead>
            <tr>
                <th>Datei</th>
                <th>Tags</th>
                <th>Hochgeladen am</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
        @foreach($animations as $animation)
            <tr>
                <td>
                    @php
                        $thumbnailPath = public_path('uploads/thumbnails/thumb_' . $animation->file_name);
                    @endphp

                    @if (file_exists($thumbnailPath))
                        <img src="{{ asset('uploads/thumbnails/thumb_' . $animation->file_name) }}" alt="Thumbnail" style="width: 50px; height: auto;">
                    @endif
                    &nbsp;{{ $animation->file_name }}
                </td>
                
                <td>
                    @if($animation->tags->isNotEmpty())
                        @foreach($animation->tags as $tag)
                            <span class="badge badge-pill badge-primary">{{ $tag->name }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">Keine Tags vorhanden</span>
                    @endif
                </td>
                               
                <td>{{ $animation->created_at }}</td>
                <td>
                    <!-- Badge für gelöschte Dateien -->
                    @if (!file_exists(public_path('uploads/' . $animation->file_name)))
                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Deleted</span>
                    @else
                        <!-- Datei ansehen -->
                        <a href="{{ url('uploads/' . $animation->file_name) }}" target="_blank" class="btn btn-link text-primary">
                            <i class="fas fa-eye"></i>
                        </a>

                        <!-- Datei herunterladen -->
                        <a href="{{ route('animations.download', $animation->file_name) }}" class="btn btn-link text-success">
                            <i class="fas fa-save"></i>
                        </a>
                    @endif
                    <!-- Datei löschen -->
                    <form action="{{ route('animations.destroy', $animation->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger" onclick="return confirm('Bist du sicher?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
