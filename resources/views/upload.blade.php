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
            <label for="files">Animation Dateien</label>
            <input type="file" id="files" name="files[]" class="form-control" multiple required>
        </div>

        <!-- Dynamisch generierte Vorschau der Dateien -->
        <div class="form-group" id="thumbnails-container"></div>

        <!-- Tags für jede Datei dynamisch hinzufügen -->
        <div class="form-group" id="tags-container"></div>

        <button type="submit" class="btn btn-primary">Hochladen</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

<script>
    document.getElementById('files').addEventListener('change', function(e) {
        const files = e.target.files;
        const tagsContainer = document.getElementById('tags-container');
        const thumbnailsContainer = document.getElementById('thumbnails-container');
        tagsContainer.innerHTML = ''; // Vorherige Felder löschen
        thumbnailsContainer.innerHTML = ''; // Vorherige Thumbnails löschen

        @php
            $allTags = \App\Models\Tag::all()->pluck('name');
        @endphp

        const allTags = @json($allTags);

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const div = document.createElement('div');
            div.className = 'form-group';

            // Input-Feld für Tags mit Tagify
            const newTagField = document.createElement('input');
            newTagField.type = 'text';
            newTagField.name = 'tags[]';
            newTagField.className = 'form-control';
            newTagField.placeholder = `Tags für ${files[i].name}`;
            div.appendChild(newTagField);
            tagsContainer.appendChild(div);

            // Tagify initialisieren
            new Tagify(newTagField, {
                whitelist: allTags,
                dropdown: {
                    maxItems: 10,           // Max. Vorschläge in der Dropdown-Liste
                    enabled: 0,             // Show suggestions on focus
                    closeOnSelect: false    // Don't close the dropdown after selecting a tag
                }
            });

            // Vorschau für Bild- und Videodateien
            const reader = new FileReader();
            reader.onload = function(event) {
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.style.width = '100px';
                    img.style.marginRight = '10px';
                    thumbnailsContainer.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = event.target.result;
                    video.controls = true;
                    video.style.width = '150px';
                    video.style.marginRight = '10px';
                    video.currentTime = 1;  // Zeigt das erste Frame des Videos
                    thumbnailsContainer.appendChild(video);
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
@endsection
