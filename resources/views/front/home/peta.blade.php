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
                        <div class="fw-bold" style="font-size:0.9rem; margin-bottom:3px;">22 Wilayah</div>
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
                        <div class="fw-bold" style="font-size:0.9rem; margin-bottom:3px;">Jaringan Kampus Nasional</div>
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
        background: #1e40af;
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
// DATA KAMPUS - Edit sesuai data kamu
// ================================================
var campusData = {
    "Jawa Timur": {
        center: [-7.536, 112.238],
        campuses: [
            { name: "Universitas Airlangga",               lat: -7.2804, lng: 112.7965, kota: "Surabaya", type: "PTN" },
            { name: "Institut Teknologi Sepuluh Nopember", lat: -7.2816, lng: 112.7951, kota: "Surabaya", type: "PTN" },
            { name: "Universitas Brawijaya",               lat: -7.9528, lng: 112.6138, kota: "Malang",   type: "PTN" },
            { name: "Universitas Negeri Malang",           lat: -7.9694, lng: 112.6323, kota: "Malang",   type: "PTN" },
            { name: "Universitas Jember",                  lat: -8.1670, lng: 113.7003, kota: "Jember",   type: "PTN" },
            { name: "Universitas Negeri Surabaya",         lat: -7.3581, lng: 112.7381, kota: "Surabaya", type: "PTN" },
        ]
    },
    "Jawa Tengah": {
        center: [-7.150, 110.140],
        campuses: [
            { name: "Universitas Diponegoro",    lat: -7.0515, lng: 110.4381, kota: "Semarang",   type: "PTN" },
            { name: "Universitas Gadjah Mada",   lat: -7.7717, lng: 110.3774, kota: "Yogyakarta", type: "PTN" },
            { name: "Universitas Sebelas Maret", lat: -7.5566, lng: 110.8567, kota: "Surakarta",  type: "PTN" },
        ]
    },
    "Jawa Barat": {
        center: [-6.903, 107.618],
        campuses: [
            { name: "Institut Teknologi Bandung",       lat: -6.8915, lng: 107.6107, kota: "Bandung", type: "PTN" },
            { name: "Universitas Padjadjaran",          lat: -6.9218, lng: 107.7706, kota: "Bandung", type: "PTN" },
            { name: "Universitas Pendidikan Indonesia", lat: -6.8615, lng: 107.5944, kota: "Bandung", type: "PTN" },
            { name: "Institut Pertanian Bogor",         lat: -6.5591, lng: 106.7295, kota: "Bogor",   type: "PTN" },
        ]
    },
    "DKI Jakarta": {
        center: [-6.208, 106.845],
        campuses: [
            { name: "Universitas Indonesia",      lat: -6.3612, lng: 106.8268, kota: "Depok",   type: "PTN" },
            { name: "Universitas Negeri Jakarta", lat: -6.2001, lng: 106.9005, kota: "Jakarta", type: "PTN" },
            { name: "Universitas Trisakti",       lat: -6.1676, lng: 106.7996, kota: "Jakarta", type: "PTS" },
            { name: "Bina Nusantara University",  lat: -6.2007, lng: 106.7812, kota: "Jakarta", type: "PTS" },
        ]
    },
    "Kalimantan Timur": {
        center: [-0.538, 116.419],
        campuses: [
            { name: "Universitas Mulawarman",      lat: -0.4646, lng: 117.1481, kota: "Samarinda",  type: "PTN" },
            { name: "Politeknik Negeri Samarinda", lat: -0.4726, lng: 117.1411, kota: "Samarinda",  type: "PTN" },
            { name: "Universitas Balikpapan",      lat: -1.2372, lng: 116.8529, kota: "Balikpapan", type: "PTS" },
        ]
    },
    "Sulawesi Selatan": {
        center: [-5.147, 119.432],
        campuses: [
            { name: "Universitas Hasanuddin",      lat: -5.1337, lng: 119.4880, kota: "Makassar", type: "PTN" },
            { name: "Universitas Negeri Makassar", lat: -5.1499, lng: 119.4328, kota: "Makassar", type: "PTN" },
            { name: "UIN Alauddin Makassar",       lat: -5.2021, lng: 119.5010, kota: "Makassar", type: "PTN" },
        ]
    },
    "Sumatera Utara": {
        center: [2.115, 99.545],
        campuses: [
            { name: "Universitas Sumatera Utara", lat: 3.5648, lng: 98.6785, kota: "Medan", type: "PTN" },
            { name: "Universitas Negeri Medan",   lat: 3.5977, lng: 98.7050, kota: "Medan", type: "PTN" },
        ]
    },
    // ================================================
    // TAMBAHKAN PROVINSI BARU DI SINI:
    // "Nama Provinsi": {
    //     center: [LAT, LNG],
    //     campuses: [
    //         { name: "Nama Kampus", lat: LAT, lng: LNG, kota: "Kota", type: "PTN" },
    //     ]
    // },
    // ================================================
};

// ================================================
// ICON PIN SVG (mirip Google Maps)
// PTN = biru (#2563eb), PTS = ungu (#7c3aed)
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

var iconPTN = buatIconPin('#2563eb');
var iconPTS = buatIconPin('#7c3aed');

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
    var badgeClass = kampus.type === 'PTN' ? 'badge-ptn' : 'badge-pts';
    var tipeLabel  = kampus.type === 'PTN' ? 'Perguruan Tinggi Negeri' : 'Perguruan Tinggi Swasta';
    return '<div class="popup-head">🏛️ ' + kampus.name + '</div>'
         + '<div class="popup-body">'
         + '<div class="row-info"><span>📍</span><span>' + kampus.kota + ', ' + provinsi + '</span></div>'
         + '<div class="row-info"><span>🏫</span><span>' + tipeLabel + '</span></div>'
         + '<span class="popup-badge ' + badgeClass + '">' + kampus.type + '</span>'
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
            icon: kampus.type === 'PTS' ? iconPTS : iconPTN
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
