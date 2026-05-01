@extends('front.layouts.app', ['bodyClass' => 'page-card-member'])

@section('title', 'Aktivasi Anggota')

@section('content')
    <div class="container">
        <div class="card-member-hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <p class="mb-2 fs-6 text-light opacity-75">Tentang &gt; KTA</p>
                <h1 class="display-5 fw-bold mb-3">Kartu Tanda Anggota</h1>
                <p class="fs-6 lh-base">Organisasi pergerakan mahasiswa yang berkomitmen menumbuhkan moderasi beragama dan
                    memperkuat semangat bela negara di tengah keberagaman Indonesia.</p>
            </div>
        </div>
    </div>

    <div class="container my-5 pt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <form>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-custom" placeholder="Nama Lengkap">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-custom" placeholder="Nama Panggilan">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-custom" placeholder="Tempat Lahir">
                        </div>
                        <div class="col-md-6">
                            <select class="form-select form-select-custom">
                                <option selected disabled value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-custom" placeholder="Perguruan Tinggi">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-custom" placeholder="Pimpinan Wilayah">
                        </div>
                        <div class="col-md-6">
                            <input type="email" class="form-control form-control-custom" placeholder="Email">
                        </div>
                        <div class="col-md-6">
                            <input type="tel" class="form-control form-control-custom" placeholder="No HP">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-custom d-inline-flex align-items-center">
                            Daftar <i class="fa-solid fa-magnifying-glass ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
