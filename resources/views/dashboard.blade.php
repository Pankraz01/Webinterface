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
        <button type="submit" class="btn btn-warning">
            <i class="fas fa-trash"></i> Forget Deleted Files
        </button>
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
