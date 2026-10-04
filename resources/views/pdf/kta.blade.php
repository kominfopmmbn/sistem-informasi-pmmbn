<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $kta->member->full_name }}</title>
    <style>
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 400;
            src: url('{{ $poppinsRegularBase64 }}') format('truetype');
        }

        /*
         * dompdf tidak melipat 600 ke bold — 600 adalah subtype tersendiri, dan kalau face-nya
         * tak terdaftar Poppins malah dilewati ke fallback DejaVu. Jadi face ini wajib ada.
         */
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 600;
            src: url('{{ $poppinsSemiBoldBase64 }}') format('truetype');
        }

        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 700;
            src: url('{{ $poppinsBoldBase64 }}') format('truetype');
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'Poppins', 'DejaVu Sans', sans-serif;
            position: relative;
        }

        .card {
            width: 171.2mm;
            height: 107.96mm;
            position: relative;
            overflow: hidden;
        }

        .bg-layer {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ $bgKtaBase64 }}');
            background-size: cover;
            background-position: center;
            z-index: 0;
            /*
             * Radius ditaruh di sini, bukan di .card: elemen inilah yang melukis background-image,
             * dan dompdf meng-clip background mengikuti border-radius elemen itu sendiri (memotong
             * anak position:absolute lewat overflow:hidden tidak bisa diandalkan di dompdf).
             * BG-KTA.png sudah punya sudut transparan bawaan ~4.5mm, jadi nilai di bawah itu tidak
             * akan terlihat efeknya — PNG-nya yang berkuasa.
             */
            border-radius: 5mm;
        }

        /* Header kiri-atas: logo + nama organisasi */
        .logo {
            position: absolute;
            top: 9mm;
            left: 10mm;
            width: 17mm;
            height: 17mm;
            z-index: 1;
        }

        .org-name {
            position: absolute;
            top: 10mm;
            left: 31mm;
            width: 105mm;
            z-index: 1;
            font-size: 20px;
            color: #ffffff;
            line-height: 0.8;
        }

        /*
         * Ketiga baris dibungkus satu blok yang di-anchor dari `bottom`, bukan tiga blok
         * dengan offset masing-masing: nama panjang yang wrap jadi tumbuh ke atas tanpa
         * menabrak baris daerah/nomor, berapa pun jumlah barisnya.
         */
        .member-info {
            position: absolute;
            left: 10mm;
            bottom: 9mm;
            width: 100mm;
            z-index: 1;
            color: #ffffff;
        }

        .member-name {
            font-size: 20pt;
            font-weight: 600;
            line-height: 0.7;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .member-region {
            font-size: 20pt;
            font-weight: 600;
            line-height: 0.7;
        }

        .member-id {
            font-size: 14pt;
            line-height: 1;
        }

        .qr-wrapper {
            position: absolute;
            right: 10mm;
            bottom: 9mm;
            width: 22mm;
            height: 22mm;
            z-index: 1;
            background-color: #ffffff;
            border-radius: 3.5mm;
            /* Padding ini yang jadi quiet zone QR — QR-nya sendiri di-generate dengan margin(0). */
            padding: 2mm;
        }

        .qr-wrapper img {
            width: 100%;
            height: 100%;
            display: block;
        }
    </style>
    @if ($isWebView ?? false)
        {{-- line-height < 1 di atas adalah kompensasi dompdf; di browser bikin baris bertumpuk, jadi dinormalkan di tampilan web saja. --}}
        <style>
            .org-name { line-height: 1.1; }
            .member-name, .member-region { line-height: 1.1; }
            .member-id { line-height: 1.3; margin-top: 1mm; }
        </style>
    @endif
</head>
<body>
    <div class="card">
        <div class="bg-layer"></div>

        @if(isset($logoBase64))
            <img class="logo" src="{{ $logoBase64 }}" alt="Logo PMMBN">
        @endif

        <div class="org-name">Pergerakan Mahasiswa Moderasi<br>Beragama dan Bela Negara</div>

        <div class="member-info">
            <div class="member-name">{{ $kta->member->full_name }}</div>

            @if($regionName)
                <div class="member-region">{{ $regionName }}</div>
            @endif

            <div class="member-id">{{ $kta->number }}</div>
        </div>

        @if(isset($qrBase64))
            <div class="qr-wrapper">
                <img src="{{ $qrBase64 }}" alt="QR Code">
            </div>
        @endif
    </div>
</body>
</html>
