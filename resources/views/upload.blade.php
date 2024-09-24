@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Animation hochladen</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('animations.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Animation Datei</label>
            <input type="file" name="file" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="tags">Tags (getrennt durch Komma)</label>
            <input type="text" name="tags" class="form-control" placeholder="z.B. transition, ink, monochrom" required>
        </div>

        <button type="submit" class="btn btn-primary">Hochladen</button>
    </form>
</div>
@endsection
