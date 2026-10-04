@if ($banners->isNotEmpty())
    @php($hasMultiple = $banners->count() > 1)
    <div class="modal fade" id="bannerPopup" tabindex="-1" aria-label="Banner" aria-hidden="true"
        data-forced="{{ $bannerForced ? '1' : '0' }}">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bg-transparent">
                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <div id="bannerPopupCarousel" class="carousel slide"
                    @if ($hasMultiple) data-bs-ride="carousel" @endif>
                    @if ($hasMultiple)
                        <div class="carousel-indicators">
                            @foreach ($banners as $banner)
                                <button type="button" data-bs-target="#bannerPopupCarousel"
                                    data-bs-slide-to="{{ $loop->index }}" @class(['active' => $loop->first])
                                    @if ($loop->first) aria-current="true" @endif
                                    aria-label="Banner {{ $loop->iteration }}"></button>
                            @endforeach
                        </div>
                    @endif
                    <div class="carousel-inner">
                        @foreach ($banners as $banner)
                            <div @class(['carousel-item', 'active' => $loop->first]) data-banner-slug="{{ $banner->slug }}">
                                <img src="{{ $banner->getFirstMediaUrl(\App\Models\Banner::IMAGE_COLLECTION) }}"
                                    alt="{{ $banner->title }}" class="d-block w-100 rounded-3"
                                    style="max-height: 75vh; object-fit: contain;">
                                @if ($banner->link_url)
                                    @php($isExternal = parse_url($banner->link_url, PHP_URL_HOST) !== request()->getHost())
                                    <div class="text-center mt-3">
                                        <a href="{{ $banner->link_url }}" class="btn btn-brand"
                                            @if ($isExternal) target="_blank" rel="noopener" @endif>Daftar Sekarang</a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if ($hasMultiple)
                        <button class="carousel-control-prev" type="button" data-bs-target="#bannerPopupCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Sebelumnya</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#bannerPopupCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Berikutnya</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var el = document.getElementById('bannerPopup');
            var key = 'bannerPopupSeen';
            var seen = false;
            try { seen = sessionStorage.getItem(key) === '1'; } catch (e) {}
            if (el.dataset.forced !== '1' && seen) {
                return;
            }
            // Tandai saat tampil, bukan saat ditutup: klik link internal pindah halaman tanpa menutup modal.
            el.addEventListener('shown.bs.modal', function () {
                try { sessionStorage.setItem(key, '1'); } catch (e) {}
            });
            bootstrap.Modal.getOrCreateInstance(el).show();
        })();
    </script>
@endif
