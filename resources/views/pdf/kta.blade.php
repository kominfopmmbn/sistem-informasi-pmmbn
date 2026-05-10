<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $kta->member->full_name }}</title>
    <style>
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
            font-family: 'DejaVu Sans', sans-serif;
            background-color: #8b0000;
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
        }

        /* Kiri: Logo — pakai position absolute */
        .left-col {
            position: absolute;
            top: 0;
            left: 0;
            width: 42%;        /* ← pakai % bukan mm */
            height: 100%;
            z-index: 1;
            padding: 10mm 0 0 8mm;
        }

        .logo {
            width: 23mm;
            height: 23mm;
        }

        /* Kanan: Konten — mulai dari 72mm */
        .right-col {
            position: absolute;
            top: 0;
            left: 32%;         /* ← sama dengan width left-col */
            width: 60%;        /* ← sisa ruang */
            height: 100%;
            z-index: 1;
            padding: 10% 6% 5% 2%;  /* ← tambah padding kanan lebih besar */
            text-align: right;
        }

        .member-name {
            font-size: 18pt;           /* ← kecilkan font agar tidak terpotong */
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.5px;
            line-height: 1.1;
            margin-bottom: 4mm;
            text-transform: uppercase;
            word-break: break-word;    /* ← agar nama panjang tidak terpotong */
            overflow-wrap: break-word;
        }

        .divider {
            width: 25%;
            height: 1.5px;
            background-color: #ffffff;
            margin-bottom: 3%;
            margin-left: auto;
            margin-right: 0;
        }

        .member-id {
            font-size: 9pt;
            font-weight: 400;
            color: #e8a0a0;
            letter-spacing: 3px;
            margin-bottom: 5%;
        }

        .qr-wrapper {
            width: 23%;
            height: auto;
            border: 2px solid #ffffff;
            border-radius: 3mm;
            overflow: hidden;
            margin-left: auto;
            margin-right: 0;
        }

        .qr-wrapper img {
            width: 100%;
            height: auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="bg-layer"></div>

        <!-- Kiri: Logo -->
        <div class="left-col">
            @if(isset($logoBase64))
                <img class="logo" src="{{ $logoBase64 }}" alt="Logo PMMBN">
            @endif
        </div>

        <!-- Kanan: Info anggota -->
        <div class="right-col">
            <div class="member-name">{{ $kta->member->full_name }}</div>
            <div class="divider"></div>
            <div class="member-id">{{ $kta->number }}</div>

            @if(isset($qrBase64))
                <div class="qr-wrapper">
                    <img src="{{ $qrBase64 }}" alt="QR Code">
                </div>
            @endif
        </div>
    </div>
</body>
</html>
