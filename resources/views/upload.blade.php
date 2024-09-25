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

        <!-- Tags für jede Datei dynamisch hinzufügen -->
        <div class="form-group" id="tags-container">
            <!-- Die dynamisch generierten Tag-Felder erscheinen hier -->
        </div>

        <button type="submit" class="btn btn-primary">Hochladen</button>
    </form>
</div>

<script>
    document.getElementById('files').addEventListener('change', function(e) {
        const files = e.target.files;
        const container = document.getElementById('tags-container');
        container.innerHTML = ''; // Vorherige Felder löschen

        for (let i = 0; i < files.length; i++) {
            const div = document.createElement('div');
            div.className = 'form-group';

            // Input-Feld für Tags
            const newTagField = document.createElement('input');
            newTagField.type = 'text';
            newTagField.name = 'tags[]';
            newTagField.className = 'form-control tagify-input';
            newTagField.placeholder = `Tags für ${files[i].name}`;

            div.appendChild(newTagField);
            container.appendChild(div);

            // Tagify initialisieren
            new Tagify(newTagField, {
                whitelist: @json($tags),  // Existierende Tags aus der Datenbank
                maxTags: 10,
                dropdown: {
                    maxItems: 20,
                    classname: "tags-look",
                    enabled: 0, // zeigt die Vorschläge sofort an
                    closeOnSelect: false
                }
            });
        }
    });
</script>

@endsection
