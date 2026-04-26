<!-- SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script>
    // Initialize Animations
    AOS.init({
        once: true, // Animasi hanya berjalan sekali saat di-scroll
        offset: 50,  // Jarak trigger animasi
    });
</script>
@include('front.helpers.front-js-helpers')
