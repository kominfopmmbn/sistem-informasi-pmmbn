@php
    $selectedRoleId = old(
        'role_id',
        isset($user) ? $user->roles->first()?->id : null
    );
@endphp

<div class="row g-6">
    <div class="col-12 col-md-6">
        <label class="form-label" for="user_name">Nama</label>
        <input type="text" name="name" id="user_name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $user->name ?? '') }}" required maxlength="255" autocomplete="name">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="user_email">Email</label>
        <input type="email" name="email" id="user_email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $user->email ?? '') }}" required maxlength="255" autocomplete="email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="user_password">Password @if (isset($user))<span class="text-body-secondary fw-normal">(kosongkan bila tidak diubah)</span>@endif</label>
        <input type="password" name="password" id="user_password"
            class="form-control @error('password') is-invalid @enderror"
            @if (!isset($user)) required @endif autocomplete="new-password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="user_password_confirmation">Konfirmasi password</label>
        <input type="password" name="password_confirmation" id="user_password_confirmation" class="form-control"
            @if (!isset($user)) required @endif autocomplete="new-password">
    </div>
    <div class="col-12">
        <label class="form-label" for="user_role_id">Peran</label>
        <div class="select2-primary">
            <div class="position-relative w-100">
                <select name="role_id" id="user_role_id" class="select2 form-select"
                    data-placeholder="Tanpa peran">
                    <option value=""></option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) $selectedRoleId === (string) $role->id)>
                            {{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @error('role_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        'use strict';
        $(function() {
            const $role = $('#user_role_id');
            if ($role.length && typeof $.fn.select2 !== 'undefined') {
                $role.wrap('<div class="position-relative"></div>');
                $role.select2({
                    placeholder: $role.data('placeholder') || 'Tanpa peran',
                    allowClear: true,
                    dropdownParent: $role.parent(),
                    width: '100%'
                });
                // Nilai tidak terkirim jika native select tetap disabled saat submit
                $role.closest('form').on('submit', function() {
                    $role.prop('disabled', false);
                });
            }
        });
    </script>
@endpush
