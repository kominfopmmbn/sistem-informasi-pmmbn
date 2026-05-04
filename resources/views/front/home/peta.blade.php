<!-- PETA SEBARAN -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-4 text-start">Sebaran Wilayah</h2>
        <div id="map" style="height: 500px; width: 100%;" class="rounded"></div>
    </div>
</section>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([-2.5, 118], 5); // Koordinat tengah Indonesia

        // Tambahkan layer peta (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Contoh marker untuk sebaran wilayah
        var marker1 = L.marker([-0.7893, 113.9213]).addTo(map).bindPopup('Sumatera, Jawa, Bali');
        var marker2 = L.marker([-0.7893, 110.9213]).addTo(map).bindPopup('Kalimantan, Sulawesi');
        var marker3 = L.marker([-0.7893, 120.9213]).addTo(map).bindPopup('Nusa Tenggara, Maluku, Papua');
    </script>
@endpush
