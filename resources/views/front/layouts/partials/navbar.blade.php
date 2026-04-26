<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm py-3 main-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ asset('assets/img/logo/pmmbn.png') }}" alt="Logo" width="40" height="40"
                class="d-inline-block align-text-top me-2">
            <span class="fw-bold fs-6 text-wrap" style="max-width: 200px; line-height: 1.2;">Pergerakan Mahasiswa
                Moderasi dan Bela Negara </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center fw-medium">
                <li class="nav-item"><a class="nav-link active text-brand" aria-current="page"
                        href="#">Beranda</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="tentangDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Tentang
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="tentangDropdown">
                        <li><a class="dropdown-item" href="tentang.html">Sejarah</a></li>
                        <li><a class="dropdown-item" href="#">Struktural</a></li>
                        <li><a class="dropdown-item" href="#">AD/ART</a></li>
                        <li><a class="dropdown-item" href="#">KTA</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="artikelDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Artikel
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="artikelDropdown">
                        <li><a class="dropdown-item" href="news.html">Berita</a></li>
                        <li><a class="dropdown-item" href="news.html">Opini</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="#">Program Unggulan</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Download</a></li>
            </ul>
        </div>
    </div>
</nav>
