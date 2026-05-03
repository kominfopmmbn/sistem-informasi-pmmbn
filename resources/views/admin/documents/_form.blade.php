@php
    use App\Models\Document;
@endphp

<div class="row g-6">
    <div class="col-12">
        <label class="form-label" for="document_title">Judul</label>
        <input type="text" name="title" id="document_title"
            class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', isset($document) ? $document->title : '') }}" required maxlength="255"
            autocomplete="off">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="document_file">Berkas</label>
        <input type="file" name="file" id="document_file"
            class="form-control @error('file') is-invalid @enderror"
            @if (! isset($document)) required @endif
            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.jpg,.jpeg,.png,.gif,.webp,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
        @error('file')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @isset($document)
            @php
                $current = $document->getFirstMedia(Document::FILE_COLLECTION);
            @endphp
            @if ($current !== null)
                <p class="form-text mb-0">
                    Berkas saat ini:
                    <a href="{{ $current->getUrl() }}" target="_blank" rel="noopener noreferrer">{{ $current->file_name }}</a>.
                    Unggah berkas baru untuk menggantinya.
                </p>
            @else
                <p class="form-text text-body-secondary mb-0">Belum ada berkas.</p>
            @endif
        @else
            <p class="form-text text-body-secondary mb-0">Satu berkas per dokumen (PDF, Office, gambar, ZIP, atau teks).</p>
        @endisset
    </div>
</div>
