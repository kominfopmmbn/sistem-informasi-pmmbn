<div class="row g-6">
    <div class="col-12 col-md-4">
        <label class="form-label" for="regional_leader_code">Kode</label>
        <input type="text" name="code" id="regional_leader_code"
            class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', isset($regionalLeader) ? $regionalLeader->code : '') }}" required maxlength="50">
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-8">
        <label class="form-label" for="regional_leader_name">Nama</label>
        <input type="text" name="name" id="regional_leader_name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', isset($regionalLeader) ? $regionalLeader->name : '') }}" required maxlength="255">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
