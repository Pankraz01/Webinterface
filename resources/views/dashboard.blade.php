@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Animationsliste</h2>

    <!-- Filter-Formular -->
    <form method="GET" action="{{ route('dashboard') }}">
        <div class="form-group">
            <label for="tag">Nach Tag filtern</label>
            <input type="text" name="tag" class="form-control" placeholder="z.B. transition, ink, monochrom">
        </div>
        <button type="submit" class="btn btn-primary">Filtern</button>
    </form>

    <form action="{{ route('animations.forgetDeleted') }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-warning">Forget Deleted Files</button>
    </form>


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
                <td>{{ $animation->file_name }}</td>
                <td>{{ $animation->tags }}</td>
                <td>{{ $animation->created_at }}</td>
                <td>
                    @if (!file_exists(public_path('uploads/' . $animation->file_name)))
                        <span class="badge bg-danger">Deleted</span>
                    @endif

                    <!-- Datei ansehen, herunterladen, löschen -->
                    <a href="{{ url('uploads/' . $animation->file_name) }}" target="_blank" class="btn btn-info">Ansehen</a>
                    <a href="{{ route('animations.download', $animation->file_name) }}" class="btn btn-success">Herunterladen</a>
                    <form action="{{ route('animations.destroy', $animation->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Bist du sicher?')">Löschen</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
