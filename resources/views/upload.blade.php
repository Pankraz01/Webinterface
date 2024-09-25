@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Animation hochladen</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form id="uploadForm" action="{{ route('animations.upload.worker') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="files">Animation Dateien</label>
            <input type="file" id="files" name="files[]" class="form-control" multiple required>
        </div>

        <!-- Progressbar -->
        <div class="progress mb-3" style="height: 25px; display: none;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
        </div>

        <!-- Tags für jede Datei dynamisch hinzufügen -->
        <div class="form-group" id="tags-container">
            <!-- Dynamisch generierte Felder kommen hier hin -->
        </div>

        <button type="submit" class="btn btn-primary">Hochladen</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

<script>
    document.getElementById('files').addEventListener('change', function(e) {
        const files = e.target.files;
        const container = document.getElementById('tags-container');
        container.innerHTML = ''; // Vorherige Felder löschen

        @php
            $allTags = \App\Models\Tag::all()->pluck('name');
        @endphp

        const allTags = @json($allTags);

        for (let i = 0; i < files.length; i++) {
            const div = document.createElement('div');
            div.className = 'form-group';

            const thumbnail = document.createElement('img');
            thumbnail.style.width = "150px";
            thumbnail.style.height = "auto";
            thumbnail.style.marginBottom = "10px";
            thumbnail.src = URL.createObjectURL(files[i]);

            const newTagField = document.createElement('input');
            newTagField.type = 'text';
            newTagField.name = 'tags[]';
            newTagField.className = 'form-control';
            newTagField.placeholder = `Tags für ${files[i].name}`;
            newTagField.setAttribute('list', `tags-list-${i}`);

            // Datalist für Autocomplete
            const dataList = document.createElement('datalist');
            dataList.id = `tags-list-${i}`;

            allTags.forEach(tag => {
                const option = document.createElement('option');
                option.value = tag;
                dataList.appendChild(option);
            });

            div.appendChild(thumbnail);
            div.appendChild(newTagField);
            div.appendChild(dataList);
            container.appendChild(div);

            // Tagify für die Eingabefelder aktivieren
            new Tagify(newTagField, {
                whitelist: allTags,
                dropdown: {
                    maxItems: 20,
                    classname: "tags-look",
                    enabled: 0,
                    closeOnSelect: false
                }
            });
        }
    });

    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        const progressBar = document.querySelector('.progress-bar');
        const progressWrapper = document.querySelector('.progress');

        progressWrapper.style.display = 'block';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route('animations.upload.worker') }}', true);

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
                progressBar.textContent = Math.round(percentComplete) + '%';
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                location.reload(); // Erfolgreiches Hochladen, Seite neu laden
            } else {
                alert('Fehler beim Hochladen der Datei!');
            }
        };

        xhr.send(formData);
    });
</script>

@endsection
