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

        <!-- Container für die dynamischen Elemente -->
        <div id="dynamic-fields-container"></div>

        <button type="submit" class="btn btn-primary">Hochladen</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

<script>
    document.getElementById('files').addEventListener('change', function(e) {
        const files = e.target.files;
        const dynamicContainer = document.getElementById('dynamic-fields-container');
        dynamicContainer.innerHTML = ''; // Vorherige Felder löschen

        @php
            $allTags = \App\Models\Tag::all()->pluck('name');
        @endphp

        const allTags = @json($allTags);

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const wrapperDiv = document.createElement('div');
            wrapperDiv.className = 'form-group d-flex align-items-center'; // Flexbox für horizontale Anordnung

            // Vorschau für Bild- und Videodateien
            const reader = new FileReader();
            reader.onload = function(event) {
                let previewElement;
                if (file.type.startsWith('image/')) {
                    previewElement = document.createElement('img');
                    previewElement.src = event.target.result;
                    previewElement.style.width = '100px';
                    previewElement.style.height = 'auto';
                    previewElement.style.marginRight = '10px'; // Abstand zum Eingabefeld
                } else if (file.type.startsWith('video/')) {
                    previewElement = document.createElement('video');
                    previewElement.src = event.target.result;
                    previewElement.controls = true;
                    previewElement.style.width = '150px';
                    previewElement.style.marginRight = '10px'; // Abstand zum Eingabefeld
                    previewElement.currentTime = 1;
                }

                wrapperDiv.appendChild(previewElement);
            };
            reader.readAsDataURL(file);

            // Input-Feld für Tags mit Tagify
            const newTagField = document.createElement('input');
            newTagField.type = 'text';
            newTagField.name = 'tags[]';
            newTagField.className = 'form-control';
            newTagField.placeholder = `Tags für ${file.name}`;

            wrapperDiv.appendChild(newTagField);
            dynamicContainer.appendChild(wrapperDiv);

            // Tagify initialisieren
            new Tagify(newTagField, {
                whitelist: allTags,
                dropdown: {
                    maxItems: 10,           // Max. Vorschläge in der Dropdown-Liste
                    enabled: 0,             // Show suggestions on focus
                    closeOnSelect: false    // Dropdown nicht nach Auswahl schließen
                }
            });
        }
    });
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
@endsection
