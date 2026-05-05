<!-- HEADER SECTION -->
<section class="py-5 my-5">
    <div class="container">
        <div class="row align-items-center" id="bootstrap-video-gallery">

            {{-- Kolom Thumbnail Video --}}
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-duration="1000">
                <div class="position-relative" style="cursor: pointer;" data-bs-toggle="modal"
                    data-bs-target="#videoModal"
                    onclick="document.getElementById('ytFrame').src='https://www.youtube.com/embed/awd9QeAiQGA?autoplay=1&rel=0'">

                    <img class="img-fluid rounded w-100" src="https://img.youtube.com/vi/awd9QeAiQGA/maxresdefault.jpg"
                        alt="Thumbnail Video"
                        onerror="this.src='https://img.youtube.com/vi/awd9QeAiQGA/hqdefault.jpg'" />

                    <div style="position:absolute; top:50%; left:50%;
                transform:translate(-50%,-50%);
                width:64px; height:64px;
                background:rgba(220,30,30,0.9);
                border-radius:50%; display:flex;
                align-items:center; justify-content:center;
                pointer-events:none;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                            <polygon points="9,7 19,12 9,17" />
                        </svg>
                    </div>

                </div>
            </div>

            {{-- Kolom Teks --}}
            <div class="col-lg-6 ps-lg-5" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                <p class="text-brand fw-bold mb-1">Tentang Kami</p>
                <h2 class="fw-bold mb-4">Bergerak dengan Cinta Mengabdi dengan Rasa</h2>
                <p class="text-muted">Gerakan ini lahir dari kepedulian pemuda terhadap kondisi sosial masyarakat.
                    Kami percaya bahwa setiap langkah kecil yang dilakukan bersama-sama akan membawa perubahan besar
                    bagi kemajuan bangsa. Kami berkomitmen untuk terus mengabdi dengan tulus dan ikhlas.</p>
                <a href="#" class="btn btn-outline-brand mt-3">Selengkapnya</a>
            </div>

        </div>
    </div>
</section>

{{-- ===== MODAL VIDEO ===== --}}
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true"
    onclick="if(event.target===this){ document.getElementById('ytFrame').src=''; }">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    onclick="document.getElementById('ytFrame').src = ''">
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9">
                    <iframe id="ytFrame" src="" frameborder="0" allowfullscreen allow="autoplay; encrypted-media">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>


@push('script')
<script>
    document.getElementById('videoModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('ytFrame').src = '';
  });
</script>
@endpush
