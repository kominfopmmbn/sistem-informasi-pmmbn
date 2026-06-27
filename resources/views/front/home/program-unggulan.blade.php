@php
    use App\Models\Program;
    use Illuminate\Support\Str;

    $firstProgram = $programs->first();
    $showcaseCover = $firstProgram
        ? ($firstProgram->getFirstMediaUrl(Program::COVER_COLLECTION)
            ?: 'https://placehold.co/1200x1400/5e0d0d/ffffff?text=' . urlencode($firstProgram->title))
        : 'https://placehold.co/1200x1400/5e0d0d/ffffff?text=Program+Unggulan';
@endphp

<!-- PROGRAM UNGGULAN -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5" data-aos="fade-up">
            <div>
                <p class="text-brand fw-bold mb-1">Program Unggulan</p>
                <h2 class="fw-bold">Program Unggulan</h2>
                <p class="text-muted mt-2 mb-0" style="max-width: 600px;">Program unggulan kami dirancang sebagai
                    ruang aksi nyata dan sarana pengembangan diri bagi para anggota.</p>
            </div>
            <a href="{{ route('program-unggulan.index') }}" class="btn btn-outline-brand d-none d-md-block">Lihat Semua <i
                    class="bi bi-arrow-right ms-1"></i></a>
        </div>

        <div class="program-showcase overflow-hidden rounded-4">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-6">
                    <div class="program-showcase-image">
                        <img id="programShowcaseImg" src="{{ $showcaseCover }}"
                            alt="Dokumentasi Program PMMBN" class="w-100 h-100 object-fit-cover">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="program-showcase-content">

                        <div class="row g-4 flex-nowrap card-slider-container" id="cardSlider">

                            @forelse ($programs as $program)
                                @php
                                    $cover = $program->getFirstMediaUrl(Program::COVER_COLLECTION)
                                        ?: 'https://placehold.co/1200x1400/5e0d0d/ffffff?text=' . urlencode($program->title);
                                @endphp
                                <div class="col-md-4 shrink-0" data-aos="fade-up" data-aos-delay="{{ 100 + $loop->index * 100 }}">
                                    <div class="card program-card {{ $loop->first ? 'active' : '' }} p-4 {{ $loop->first ? 'shadow' : 'shadow-sm' }} border-0"
                                        data-cover="{{ $cover }}">
                                        <h4 class="fw-bold">{{ $program->title }}</h4>
                                        <p class="mt-3 opacity-75">{{ Str::limit($program->excerpt, 160) }}</p>
                                        <div class="mt-auto text-end">
                                            <a href="{{ route('program-unggulan.show', $program->slug) }}"
                                                class="text-reset" aria-label="Lihat program {{ $program->title }}">
                                                <i class="bi bi-arrow-up-right-circle fs-3 opacity-75"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted text-center py-5 mb-0">Belum ada program unggulan.</p>
                                </div>
                            @endforelse

                        </div>

                        @if ($programs->isNotEmpty())
                            <div class="program-nav d-flex justify-content-end gap-3 mt-4">
                                <button id="btnPrev" class="btn p-0 border-0 bg-transparent" type="button"
                                    aria-label="Program Sebelumnya">
                                    <i class="bi bi-arrow-left-circle fs-4 text-secondary opacity-50 nav-icon"></i>
                                </button>
                                <button id="btnNext" class="btn p-0 border-0 bg-transparent" type="button"
                                    aria-label="Program Selanjutnya">
                                    <i class="bi bi-arrow-right-circle fs-4 text-brand nav-icon"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
    const slider = document.getElementById("cardSlider");
    const cards = document.querySelectorAll(".program-card");
    const btnPrev = document.getElementById("btnPrev");
    const btnNext = document.getElementById("btnNext");
    const showcaseImg = document.getElementById("programShowcaseImg");

    // Pastikan tombol ada di halaman sebelum menjalankan logika
    if (slider && btnPrev && btnNext) {
        const iconPrev = btnPrev.querySelector("i");
        const iconNext = btnNext.querySelector("i");

        let currentIndex = 0;
        const totalCards = cards.length;

        function updateSlider(scroll = true) {
            // Ganti class active pada card
            cards.forEach((card, index) => {
                if (index === currentIndex) {
                    card.classList.add("active");
                    card.classList.replace("shadow-sm", "shadow");
                } else {
                    card.classList.remove("active");
                    card.classList.replace("shadow", "shadow-sm");
                }
            });

            // Sinkronkan gambar besar di kiri dengan card yang aktif
            if (showcaseImg && cards[currentIndex] && cards[currentIndex].dataset.cover) {
                showcaseImg.src = cards[currentIndex].dataset.cover;
            }

            // Ganti visual icon navigasi
            if (currentIndex === 0) {
                iconPrev.className = "bi bi-arrow-left-circle fs-4 text-secondary opacity-50 nav-icon disabled-btn";
                iconNext.className = "bi bi-arrow-right-circle fs-4 text-brand nav-icon active-btn";
            } else if (currentIndex === totalCards - 1) {
                iconNext.className = "bi bi-arrow-right-circle fs-4 text-secondary opacity-50 nav-icon disabled-btn";
                iconPrev.className = "bi bi-arrow-left-circle fs-4 text-brand nav-icon active-btn";
            } else {
                iconPrev.className = "bi bi-arrow-left-circle fs-4 text-brand nav-icon active-btn";
                iconNext.className = "bi bi-arrow-right-circle fs-4 text-brand nav-icon active-btn";
            }

            // Geser posisi scroll menyesuaikan card yang aktif (lewati saat dipicu oleh scroll manual)
            if (scroll) {
                const activeCardWrap = cards[currentIndex].parentElement;
                slider.scrollTo({
                    left: activeCardWrap.offsetLeft - slider.offsetLeft,
                    behavior: 'smooth'
                });
            }
        }

        // Event Listener untuk Tombol Next
        btnNext.addEventListener("click", () => {
            if (currentIndex < totalCards - 1) {
                currentIndex++;
                updateSlider();
            }
        });

        // Event Listener untuk Tombol Prev
        btnPrev.addEventListener("click", () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateSlider();
            }
        });

        // Klik card langsung mengaktifkan card tersebut
        cards.forEach((card, index) => {
            card.addEventListener("click", () => {
                currentIndex = index;
                updateSlider();
            });
        });

        // Saat slider di-swipe/scroll (mobile), aktifkan card terdekat tanpa scroll ulang
        let scrollTimer;
        slider.addEventListener("scroll", () => {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(() => {
                let nearest = 0,
                    min = Infinity;
                cards.forEach((card, i) => {
                    const dist = Math.abs((card.parentElement.offsetLeft - slider.offsetLeft) - slider.scrollLeft);
                    if (dist < min) {
                        min = dist;
                        nearest = i;
                    }
                });
                if (nearest !== currentIndex) {
                    currentIndex = nearest;
                    updateSlider(false);
                }
            }, 120);
        });
    }
});
</script>
@endpush
