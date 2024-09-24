@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Animation hochladen</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    <form action="{{ route('animations.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="files">Animation Dateien</label>
            <input type="file" id="file-input" name="files[]" class="form-control" multiple required>
        </div>

        <div id="tags-container">
            <label for="tags">Tags (getrennt durch Komma)</label>
            <div id="tag-inputs"></div> <!-- Hier werden die Tag-Inputs hinzugefügt -->
        </div>

        <button type="submit" class="btn btn-primary">Hochladen</button>
    </form>

    <script>
        const fileInput = document.getElementById('file-input');
        const tagInputsContainer = document.getElementById('tag-inputs');

        fileInput.addEventListener('change', function(event) {
            const files = event.target.files;
            updateTagFields(files);
        });

        function updateTagFields(files) {
            // Leere den Container für die Tag-Inputs
            tagInputsContainer.innerHTML = '';

            // Erstelle Tag-Inputs für jede hochgeladene Datei
            Array.from(files).forEach((file, index) => {
                const tagWrapper = document.createElement('div');
                tagWrapper.className = 'form-group d-flex align-items-center mb-2';

                // Thumbnail
                const thumbnail = document.createElement('img');
                thumbnail.src = URL.createObjectURL(file); // Erstelle ein URL-Objekt für die Vorschau
                thumbnail.style.width = '50px'; // Setze die Breite des Thumbnails
                thumbnail.style.height = 'auto'; // Behalte das Seitenverhältnis
                thumbnail.className = 'me-2'; // Margin-Ende

                // Tag-Input
                const tagInput = document.createElement('input');
                tagInput.type = 'text';
                tagInput.name = 'tags[]';
                tagInput.className = 'form-control';
                tagInput.placeholder = 'Tags für ' + file.name;
                tagInput.required = true;

                // Füge Thumbnail und Input in das Wrapper-Element ein
                tagWrapper.appendChild(thumbnail);
                tagWrapper.appendChild(tagInput);

                // Füge das Wrapper-Element in den Tag-Container ein
                tagInputsContainer.appendChild(tagWrapper);
            });
        }
    </script>
</div>
@endsection
