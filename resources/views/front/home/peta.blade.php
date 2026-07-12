{{-- ============================================ --}}
{{-- SECTION: Sebaran Wilayah (Blade Template)   --}}
{{-- Letakkan di dalam layout blade kamu         --}}
{{-- ============================================ --}}

<section class="py-5" style="background:#f8fafc;">
    <div class="container">

        {{-- BARIS 1: Judul kiri + deskripsi kanan --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4 gap-3">
            <h4 class="fw-bold mb-0" style="font-size:2.75rem; min-width:200px;">Sebaran Wilayah</h4>
            <p class="mb-0 text-muted" style="max-width:420px; font-size:0.875rem; line-height:1.7;">
                Organisasi ini tumbuh sebagai pergerakan mahasiswa yang menjunjung nilai
                moderasi beragama dan bela negara. Hingga saat ini, keanggotaan kami telah
                tersebar di berbagai daerah di Indonesia.
            </p>
        </div>

        {{-- BARIS 2: 3 Stat Card --}}
        <div class="row g-3 mb-4">

            {{-- Card 1: Wilayah --}}
            <div class="col-12 col-md-4">
                <div class="d-flex align-items-start gap-3 p-3 rounded border bg-white h-100">
                    <div style="flex-shrink:0; margin-top:2px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" fill="#dc2626"/>
                            <circle cx="12" cy="9" r="2.5" fill="white"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:0.9rem; margin-bottom:3px;">{{ $provinceCount }} Wilayah</div>
                        <div class="text-muted" style="font-size:0.8rem; line-height:1.5;">
                            Sebaran kepengurusan dan anggota di berbagai daerah di Indonesia.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Jaringan Kampus --}}
            <div class="col-12 col-md-4">
                <div class="d-flex align-items-start gap-3 p-3 rounded border bg-white h-100">
                    <div style="flex-shrink:0; margin-top:2px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 3L2 8l10 5 10-5-10-5z" fill="#dc2626"/>
                            <path d="M2 16l10 5 10-5" stroke="#dc2626" stroke-width="2" stroke-linecap="round"/>
                            <path d="M2 12l10 5 10-5" stroke="#dc2626" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:0.9rem; margin-bottom:3px;">{{ $collegeCount }} Kampus</div>
                        <div class="text-muted" style="font-size:0.8rem; line-height:1.5;">
                            Anggota berasal dari berbagai perguruan tinggi negeri dan swasta.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Anggota Aktif --}}
            <div class="col-12 col-md-4">
                <div class="d-flex align-items-start gap-3 p-3 rounded border bg-white h-100">
                    <div style="flex-shrink:0; margin-top:2px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z" fill="#dc2626"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:0.9rem; margin-bottom:3px;">Ribuan Anggota Aktif</div>
                        <div class="text-muted" style="font-size:0.8rem; line-height:1.5;">
                            Mahasiswa yang terlibat dalam gerakan, kaderisasi, dan aksi sosial.
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- PETA --}}
        <div id="map" style="height: 500px; width: 100%;" class="rounded shadow-sm"></div>

    </div>
</section>

{{-- ============================================ --}}
{{-- STYLES                                       --}}
{{-- ============================================ --}}
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
<style>
    /* Pill provinsi */
    .province-pill {
        background: #8c1c03;
        color: white;
        border: 2.5px solid white;
        border-radius: 50px;
        padding: 5px 13px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: 0 3px 12px rgba(30,64,175,0.4);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: inherit;
    }
    .province-count {
        background: #f59e0b;
        color: #78350f;
        border-radius: 20px;
        padding: 1px 7px;
        font-size: 11px;
        font-weight: 800;
    }

    /* Popup styling */
    .leaflet-popup-content-wrapper {
        border-radius: 12px !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15) !important;
        padding: 0 !important;
        overflow: hidden;
    }
    .leaflet-popup-content { margin: 0 !important; width: 250px !important; }
    .popup-head {
        background: #0f172a;
        color: white;
        padding: 10px 14px;
        font-weight: 700;
        font-size: 13px;
    }
    .popup-body { padding: 10px 14px; font-size: 12.5px; color: #475569; }
    .popup-body .row-info { display:flex; gap:7px; margin-bottom:5px; color:#0f172a; align-items:flex-start; }
    .popup-badge {
        display:inline-block;
        border-radius:20px;
        padding:2px 10px;
        font-size:11px;
        font-weight:700;
        margin-top:4px;
    }
    .badge-ptn { background:#eff6ff; color:#1d4ed8; }
    .badge-pts { background:#f5f3ff; color:#6d28d9; }
</style>
@endpush

{{-- ============================================ --}}
{{-- SCRIPTS                                      --}}
{{-- ============================================ --}}
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
<script>
// ================================================
// DATA KAMPUS - dinamis dari tabel `colleges` (HomePageController)
// Struktur: { "Nama Provinsi": { center: [lat,lng], campuses: [{name,lat,lng,kota}] } }
// ================================================
var campusData = @json($campusData);

// ================================================
// ICON PIN SVG (mirip Google Maps)
// ================================================
function buatIconPin(warna) {
    var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="40" viewBox="0 0 28 40">'
            + '<path d="M14 0C6.27 0 0 6.27 0 14c0 9.75 14 26 14 26S28 23.75 28 14C28 6.27 21.73 0 14 0z" fill="' + warna + '"/>'
            + '<circle cx="14" cy="14" r="6" fill="white"/>'
            + '</svg>';
    return L.divIcon({
        html        : svg,
        className   : '',
        iconSize    : [28, 40],
        iconAnchor  : [14, 40],   // ujung bawah pin tepat di koordinat
        popupAnchor : [0, -38]    // popup muncul di atas pin
    });
}

var iconKampus = buatIconPin('#2563eb');

// ================================================
// INISIALISASI PETA
// ================================================
var ZOOM_THRESHOLD = 8; // di bawah = provinsi, di atas = kampus

var map = L.map('map').setView([-2.5, 118], 5);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 18
}).addTo(map);

// ================================================
// ICON PROVINSI (pill label)
// ================================================
function buatIconProvinsi(nama, jumlah) {
    var html = '<div class="province-pill">📍 ' + nama +
               ' <span class="province-count">' + jumlah + '</span></div>';
    return L.divIcon({ html: html, className: '', iconAnchor: [0, 14] });
}

// ================================================
// POPUP KAMPUS
// ================================================
function buatPopupKampus(kampus, provinsi) {
    return '<div class="popup-head">🏛️ ' + kampus.name + '</div>'
         + '<div class="popup-body">'
         + '<div class="row-info"><span>📍</span><span>' + kampus.kota + ', ' + provinsi + '</span></div>'
         + '</div>';
}

// ================================================
// LAYER GRUP
// ================================================
var layerProvinsi = L.layerGroup();
var layerKampus   = L.layerGroup();

Object.keys(campusData).forEach(function(provinsi) {
    var data = campusData[provinsi];

    // --- Marker provinsi (pill) ---
    var markerProvinsi = L.marker(data.center, { icon: buatIconProvinsi(provinsi, data.campuses.length) });
    markerProvinsi.bindTooltip(
        '<b>' + provinsi + '</b><br>' + data.campuses.length + ' kampus terdaftar',
        { direction: 'top', offset: [40, -8] }
    );
    markerProvinsi.on('click', function() {
        // 1 klik langsung zoom fit ke semua kampus provinsi ini
        var bounds = L.latLngBounds(data.campuses.map(function(k) { return [k.lat, k.lng]; }));
        map.fitBounds(bounds, { padding: [60, 60], animate: true, maxZoom: 12 });
    });
    layerProvinsi.addLayer(markerProvinsi);

    // --- Marker kampus (pin maps SVG) ---
    data.campuses.forEach(function(kampus) {
        var markerKampus = L.marker([kampus.lat, kampus.lng], {
            icon: iconKampus
        });
        markerKampus.bindPopup(buatPopupKampus(kampus, provinsi), { maxWidth: 280 });
        layerKampus.addLayer(markerKampus);
    });
});

// ================================================
// TOGGLE LAYER SAAT ZOOM BERUBAH
// ================================================
function updateLayer() {
    var z = map.getZoom();
    if (z >= ZOOM_THRESHOLD) {
        if (map.hasLayer(layerProvinsi))  map.removeLayer(layerProvinsi);
        if (!map.hasLayer(layerKampus))   layerKampus.addTo(map);
    } else {
        if (map.hasLayer(layerKampus))    map.removeLayer(layerKampus);
        if (!map.hasLayer(layerProvinsi)) layerProvinsi.addTo(map);
    }
}

layerProvinsi.addTo(map);
map.on('zoomend', updateLayer);
</script>
@endpush
