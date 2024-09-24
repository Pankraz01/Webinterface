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
            <label for="files">Animationen hochladen</label>
            <input type="file" name="files[]" class="form-control" multiple>
        </div>
        <button type="submit" class="btn btn-primary">Hochladen</button>
    </form>

</div>
@endsection
