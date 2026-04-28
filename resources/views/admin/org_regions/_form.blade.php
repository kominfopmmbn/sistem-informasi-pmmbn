<div class="row g-6">
    <div class="col-12 col-md-6">
        <label class="form-label" for="org_region_name">Nama</label>
        <input type="text" name="name" id="org_region_name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', isset($orgRegion) ? $orgRegion->name : '') }}" required maxlength="255" autocomplete="organization">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="org_region_code">Kode</label>
        <input type="text" name="code" id="org_region_code"
            class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', isset($orgRegion) ? $orgRegion->code : '') }}" required maxlength="255" autocomplete="off">
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
