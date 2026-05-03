@php
    $selectedIds = array_map(
        'intval',
        (array) old('permission_ids', isset($role) ? $role->permissions->pluck('id')->all() : []),
    );
    $isAdminRole = isset($role) && $role->name === 'Administrator';
@endphp

<div class="row g-6">
    <div class="col-12 col-md-6">
        <label class="form-label" for="role_name">Nama peran</label>
        <input type="text" name="name" id="role_name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $role?->name ?? '') }}" required maxlength="255" autocomplete="off"
            @if ($isAdminRole) readonly @endif>
        @if ($isAdminRole)
            <div class="form-text">Nama peran bawaan tidak dapat diubah.</div>
        @endif
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mt-6" id="role-permissions-panel">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <span class="form-label mb-0">Permission</span>
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" id="role_perm_check_all" autocomplete="off"
                aria-label="Pilih semua permission">
            <label class="form-check-label" for="role_perm_check_all">Pilih semua</label>
        </div>
    </div>
    <div class="row g-4">
        @foreach ($permissionsByGroup as $group => $groupPermissions)
            <div class="col-12">
                @php
                    $groupLabel = match ($group) {
                        'articles' => 'Artikel & opini',
                        'members' => 'Anggota',
                        'org_regions' => 'Wilayah organisasi',
                        'colleges' => 'Perguruan tinggi',
                        'provinces' => 'Provinsi (data wilayah)',
                        'cities' => 'Kota / kabupaten (data wilayah)',
                        'districts' => 'Kecamatan (data wilayah)',
                        'villages' => 'Desa / kelurahan (data wilayah)',
                        'users' => 'Users',
                        'roles' => 'Peran',
                        default => $group,
                    };
                @endphp
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <h6 class="mb-0">{{ $groupLabel }}</h6>
                    <div class="form-check mb-0">
                        <input class="form-check-input js-perm-group-master" type="checkbox" autocomplete="off"
                            data-group="{{ $group }}" id="group_master_{{ $group }}"
                            aria-label="Pilih semua permission {{ $groupLabel }}">
                        <label class="form-check-label" for="group_master_{{ $group }}">Semua di grup</label>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach ($groupPermissions as $perm)
                        <div class="col-12">
                            <div class="form-check mb-0">
                                <input class="form-check-input js-perm-item" type="checkbox" name="permission_ids[]"
                                    value="{{ $perm->id }}" id="perm_{{ $perm->id }}"
                                    data-group="{{ $group }}" @checked(in_array($perm->id, $selectedIds, true))>
                                <label class="form-check-label" for="perm_{{ $perm->id }}">
                                    {{ $perm->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @error('permission_ids')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    @if ($errors->has('permission_ids.*'))
        <div class="text-danger small mt-1">Ada permission yang tidak valid.</div>
    @endif
</div>

@push('scripts')
    <script>
        'use strict';
        (function() {
            const root = document.getElementById('role-permissions-panel');
            if (!root) {
                return;
            }
            const allInput = document.getElementById('role_perm_check_all');
            if (!allInput) {
                return;
            }

            const items = () => Array.from(root.querySelectorAll('.js-perm-item'));
            const groupMasters = () => Array.from(root.querySelectorAll('.js-perm-group-master'));

            function byGroup(g) {
                return Array.from(root.querySelectorAll(
                    '.js-perm-item[data-group="' + CSS.escape(String(g)) + '"]'
                ));
            }

            function syncGroupMaster(g) {
                const m = root.querySelector('.js-perm-group-master[data-group="' + CSS.escape(String(g)) +
                    '"]');
                if (!m) {
                    return;
                }
                const gItems = byGroup(g);
                if (gItems.length === 0) {
                    m.checked = false;
                    m.indeterminate = false;
                    return;
                }
                const n = gItems.filter(function(c) {
                    return c.checked;
                }).length;
                m.checked = n === gItems.length;
                m.indeterminate = n > 0 && n < gItems.length;
            }

            function syncGlobal() {
                const gItems = items();
                if (gItems.length === 0) {
                    allInput.checked = false;
                    allInput.indeterminate = false;
                    return;
                }
                const n = gItems.filter(function(c) {
                    return c.checked;
                }).length;
                allInput.checked = n === gItems.length;
                allInput.indeterminate = n > 0 && n < gItems.length;
            }

            allInput.addEventListener('change', function() {
                const v = allInput.checked;
                items().forEach(function(c) {
                    c.checked = v;
                });
                groupMasters().forEach(function(m) {
                    m.checked = v;
                    m.indeterminate = false;
                });
            });

            groupMasters().forEach(function(m) {
                m.addEventListener('change', function() {
                    const g = m.getAttribute('data-group');
                    if (!g) {
                        return;
                    }
                    byGroup(g).forEach(function(c) {
                        c.checked = m.checked;
                    });
                    m.indeterminate = false;
                    syncGlobal();
                });
            });

            root.addEventListener('change', function(e) {
                if (!e.target || !e.target.classList || !e.target.classList.contains('js-perm-item')) {
                    return;
                }
                const g = e.target.getAttribute('data-group');
                if (g) {
                    syncGroupMaster(g);
                }
                syncGlobal();
            });

            (function init() {
                const seen = new Set();
                items().forEach(function(c) {
                    const g = c.getAttribute('data-group');
                    if (g) {
                        seen.add(g);
                    }
                });
                seen.forEach(function(g) {
                    syncGroupMaster(g);
                });
                syncGlobal();
            }());
        }());
    </script>
@endpush
