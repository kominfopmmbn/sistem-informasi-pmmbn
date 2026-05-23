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
<script>
    (function() {
        var overlay = document.getElementById('page-transition-overlay');
        var hasNavigated = false;
        var bounceDuration = 1300;
        var transitionFlagKey = 'frontTransitionFromClick';

        if (!overlay) {
            return;
        }

        function showOverlay(withAnimation) {
            overlay.classList.add('is-visible');
            overlay.setAttribute('aria-hidden', 'false');

            if (withAnimation) {
                overlay.classList.remove('is-animating');
                void overlay.offsetWidth;
                overlay.classList.add('is-animating');
            }
        }

        function hideOverlay() {
            overlay.classList.remove('is-visible', 'is-animating');
            overlay.setAttribute('aria-hidden', 'true');
        }

        document.addEventListener('DOMContentLoaded', function() {
            var skipIntroFromNavigation = sessionStorage.getItem(transitionFlagKey) === '1';
            if (skipIntroFromNavigation) {
                sessionStorage.removeItem(transitionFlagKey);
                hideOverlay();
                return;
            }

            showOverlay(true);
            window.setTimeout(function() {
                hideOverlay();
            }, bounceDuration + 80);
        });

        document.addEventListener('click', function(event) {
            if (hasNavigated || event.defaultPrevented || event.button !== 0) {
                return;
            }

            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            var anchor = event.target.closest('a[href]');
            if (!anchor) {
                return;
            }

            if (anchor.hasAttribute('download') || anchor.target === '_blank') {
                return;
            }

            var href = anchor.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
                return;
            }

            var destination = new URL(anchor.href, window.location.href);
            var current = new URL(window.location.href);

            if (destination.origin !== current.origin) {
                return;
            }

            var samePageWithHash = destination.pathname === current.pathname &&
                destination.search === current.search &&
                destination.hash.length > 0;

            if (samePageWithHash || destination.href === current.href) {
                return;
            }

            event.preventDefault();
            hasNavigated = true;
            sessionStorage.setItem(transitionFlagKey, '1');
            showOverlay(true);

            window.setTimeout(function() {
                window.location.assign(destination.href);
            }, 180);
        });

        window.addEventListener('pageshow', function() {
            hasNavigated = false;
            hideOverlay();
        });
    })();
</script>
@include('front.helpers.front-js-helpers')
