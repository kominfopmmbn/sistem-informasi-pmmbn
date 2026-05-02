@extends('front.layouts.app', ['bodyClass' => 'page-news-detail'])

@section('title', $slug)

@section('content')
    <main class="container my-5">
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <img src="https://placehold.co/1200x500/1a1a1a/FFF?text=The+Role+of+Branding+in+UI/UX+Design"
                    alt="Hero Banner" class="hero-img shadow-sm">
            </div>
        </div>

        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h1 class="article-title">UI Design Trends in 2025<br>Ketika Desain Tidak Lagi Sekadar Visual</h1>
                <p class="article-date">10 July 2025</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-md-2 col-lg-1 mb-4">
                        <div class="share-sidebar">
                            <span class="fw-bold d-block mb-3" style="font-size: 0.9rem;">Share</span>
                            <div class="share-icons">
                                <a href="#"><i class="bi bi-instagram"></i></a>
                                <a href="#"><i class="bi bi-twitter-x"></i></a>
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7 col-lg-8 article-content pe-lg-5">
                        <p><strong>Memasuki tahun 2025</strong>, UI Design mengalami pergeseran signifikan. Desain antarmuka
                            tidak lagi dinilai dari seberapa menarik tampilannya, tetapi dari seberapa baik ia memahami
                            perilaku, emosi, dan kebutuhan pengguna. UI kini menjadi bagian penting dari strategi pengalaman
                            digital secara menyeluruh.</p>

                        <h5>1. UI yang Lebih Human-Centered</h5>
                        <p>Pendekatan human-centered design semakin dominan. Desainer tidak hanya memikirkan estetika,
                            tetapi juga empati. Elemen UI dirancang agar terasa natural, intuitif, dan minim friksi.
                            Navigasi dipersingkat, hierarki informasi diperjelas, dan micro-interaction digunakan untuk
                            membantu pengguna, bukan mengalihkan perhatian.</p>

                        <h5>2. Desain Adaptif & Personal Berbasis AI</h5>
                        <p>Integrasi AI dalam UI menjadi salah satu tren paling berpengaruh di 2025. Antarmuka kini mampu
                            menyesuaikan diri dengan kebiasaan pengguna—mulai dari rekomendasi konten, tata letak yang
                            berubah secara kontekstual, hingga preferensi warna dan ukuran teks. UI tidak lagi statis,
                            tetapi berkembang seiring perilaku pengguna.</p>

                        <h5>3. Tipografi Berani, Namun Fungsional</h5>
                        <p>Penggunaan bold typography semakin populer, bukan hanya sebagai elemen visual, tetapi sebagai
                            alat komunikasi utama. Heading yang tegas, kontras yang jelas, serta ritme tipografi yang rapi
                            membantu pengguna memahami informasi dengan lebih cepat, terutama pada produk digital berbasis
                            konten.</p>

                        <h5>4. Clean Design dengan Fokus yang Jelas</h5>
                        <p>Tren "less but better" semakin terasa. UI Design 2025 menghindari elemen yang tidak memiliki
                            fungsi jelas. White space dimanfaatkan secara strategis untuk meningkatkan fokus, keterbacaan,
                            dan kenyamanan visual. Desain yang tenang justru dianggap lebih profesional dan berkelas.</p>

                        <h5>5. Motion & Micro-Interaction yang Bermakna</h5>
                        <p>Animasi tidak lagi digunakan sekadar untuk estetika. Motion UI kini berfungsi sebagai penunjuk
                            alur, feedback interaksi, dan penguat pengalaman pengguna. Transisi dibuat halus, cepat, dan
                            konsisten agar tidak mengganggu performa maupun fokus pengguna.</p>

                        <h5>6. Aksesibilitas Menjadi Standar, Bukan Opsional</h5>
                        <p>Kesadaran akan inklusivitas semakin meningkat. UI Design 2025 menempatkan aksesibilitas sebagai
                            standar utama—mulai dari kontras warna yang aman, ukuran teks yang fleksibel, hingga navigasi
                            yang ramah untuk semua pengguna, termasuk mereka dengan keterbatasan tertentu.</p>

                        <h5>Kesimpulan</h5>
                        <p>Tren UI Design 2025 menegaskan bahwa desain bukan lagi soal "terlihat bagus", tetapi terasa
                            tepat. UI yang sukses adalah yang mampu menyatu dengan kebutuhan pengguna, mendukung tujuan
                            bisnis, dan menghadirkan pengalaman digital yang manusiawi, adaptif, dan berkelanjutan.</p>
                    </div>

                    <div class="col-md-3 col-lg-3 mt-5 mt-md-0">
                        <span class="sidebar-title">Opini Lainnya</span>
                        <div class="sidebar-item">
                            <img src="https://placehold.co/300x300/e9ecef/555?text=AI+Design" alt="Opini 1">
                        </div>
                        <div class="sidebar-item">
                            <img src="https://placehold.co/300x300/000/FFF?text=Steve+Jobs" alt="Opini 2">
                        </div>
                        <div class="sidebar-item">
                            <img src="https://placehold.co/300x300/f8f9fa/555?text=Clean+UI" alt="Opini 3">
                        </div>
                        <div class="sidebar-item">
                            <img src="https://placehold.co/300x300/1a1a1a/FFF?text=Branding+UI/UX" alt="Opini 4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
