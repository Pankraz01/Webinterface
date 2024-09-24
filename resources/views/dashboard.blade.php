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

    <!-- Datei-Tabelle -->
    <table class="table mt-4">
        <thead>
            <tr>
                <th>Datei</th>
                <th>Tags</th>
                <th>Hochgeladen am</th>
            </tr>
        </thead>
        <tbody>
            @foreach($animations as $animation)
                <tr>
                    <td>{{ $animation->file_name }}</td>
                    <td>{{ $animation->tags }}</td>
                    <td>{{ $animation->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
