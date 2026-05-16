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
            <a href="#" class="btn btn-outline-brand d-none d-md-block">Lihat Semua <i
                    class="bi bi-arrow-right ms-1"></i></a>
        </div>

        <div class="program-showcase overflow-hidden rounded-4">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-6">
                    <div class="program-showcase-image">
                        <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1400&q=80"
                            alt="Dokumentasi Program PMMBN" class="w-100 h-100 object-fit-cover">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="program-showcase-content">

                        <div class="row g-4 flex-nowrap card-slider-container" id="cardSlider">

                            <div class="col-md-4 flex-shrink-0" data-aos="fade-up" data-aos-delay="100">
                                <div class="card program-card active p-4 shadow border-0">
                                    <h4 class="fw-bold">Sekolah Moderasi</h4>
                                    <p class="mt-3 opacity-75">Program pendidikan dan kajian intensif bagi
                                        mahasiswa untuk memahami nilai toleransi, inklusivitas, dan harmoni
                                        antarumat beragama melalui diskusi, studi kasus, dan dialog lintas iman.</p>
                                    <div class="mt-auto text-end">
                                        <i class="bi bi-arrow-up-right-circle fs-3 opacity-75"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 flex-shrink-0" data-aos="fade-up" data-aos-delay="200">
                                <div class="card program-card p-4 shadow-sm border-0">
                                    <h4 class="fw-bold">Kaderisasi</h4>
                                    <p class="mt-3 opacity-75">Program pembinaan karakter kebangsaan yang
                                        menanamkan nilai cinta tanah air, kesadaran konstitusi, dan tanggung jawab
                                        sosial sebagai bagian dari bela negara dalam konteks mahasiswa.</p>
                                    <div class="mt-auto text-end">
                                        <i class="bi bi-arrow-up-right-circle fs-3 opacity-75"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 flex-shrink-0" data-aos="fade-up" data-aos-delay="300">
                                <div class="card program-card p-4 shadow-sm border-0">
                                    <h4 class="fw-bold">Digital Campaign</h4>
                                    <p class="mt-3 opacity-75">Program pengembangan intelektual melalui riset,
                                        penulisan, dan publikasi gagasan mahasiswa terkait moderasi beragama, bela
                                        negara, dan tantangan kebangsaan kontemporer.</p>
                                    <div class="mt-auto text-end">
                                        <i class="bi bi-arrow-up-right-circle fs-3 opacity-75"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 flex-shrink-0" data-aos="fade-up" data-aos-delay="400">
                                <div class="card program-card p-4 shadow-sm border-0">
                                    <h4 class="fw-bold">Program Keempat</h4>
                                    <p class="mt-3 opacity-75">Contoh program keempat yang ukurannya tetap sama dan bisa
                                        digeser ke kanan.</p>
                                    <div class="mt-auto text-end">
                                        <i class="bi bi-arrow-up-right-circle fs-3 opacity-75"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 flex-shrink-0" data-aos="fade-up" data-aos-delay="400">
                                <div class="card program-card p-4 shadow-sm border-0">
                                    <h4 class="fw-bold">Program Keempat</h4>
                                    <p class="mt-3 opacity-75">Contoh program keempat yang ukurannya tetap sama dan bisa
                                        digeser ke kanan.</p>
                                    <div class="mt-auto text-end">
                                        <i class="bi bi-arrow-up-right-circle fs-3 opacity-75"></i>
                                    </div>
                                </div>
                            </div>

                        </div>

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
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    /* Mengatur Container agar bisa digeser horizontal */
    .card-slider-container {
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scroll-behavior: smooth;
        -ms-overflow-style: none;
        /* Hide scrollbar IE/Edge */
        scrollbar-width: none;
        /* Hide scrollbar Firefox */
    }

    .card-slider-container::-webkit-scrollbar {
        display: none;
        /* Hide scrollbar Chrome/Safari */
    }

    .card-slider-container>div {
        scroll-snap-align: start;
    }

    /* Desain Card Pasif (Putih) */
    .program-card {
        background-color: #ffffff;
        color: #212529;
        /* Warna teks gelap */
        transition: all 0.3s ease;
    }

    .program-card i {
        color: #6c757d;
    }

    /* Desain Card Aktif (Merah) */
    .program-card.active {
        background-color: #8a1538;
        /* Sesuaikan dengan warna brand jika beda */
        color: #ffffff;
        transform: translateY(-5px);
        /* Efek sedikit naik */
    }

    .program-card.active i {
        color: #ffffff;
    }

    /* Warna Tombol Navigasi */
    .nav-icon {
        transition: all 0.3s ease;
    }

    .nav-icon.active-btn {
        color: #8a1538 !important;
        opacity: 1 !important;
        cursor: pointer;
    }

    .nav-icon.disabled-btn {
        color: #6c757d !important;
        opacity: 0.5 !important;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
    const slider = document.getElementById("cardSlider");
    const cards = document.querySelectorAll(".program-card");
    const btnPrev = document.getElementById("btnPrev");
    const btnNext = document.getElementById("btnNext");

    // Pastikan tombol ada di halaman sebelum menjalankan logika
    if (slider && btnPrev && btnNext) {
        const iconPrev = btnPrev.querySelector("i");
        const iconNext = btnNext.querySelector("i");

        let currentIndex = 0;
        const totalCards = cards.length;

        function updateSlider() {
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

            // Geser posisi scroll menyesuaikan card yang aktif
            const activeCardWrap = cards[currentIndex].parentElement;
            slider.scrollTo({
                left: activeCardWrap.offsetLeft - slider.offsetLeft,
                behavior: 'smooth'
            });
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
    }
});
</script>
@endpush
