<?php

namespace App\Http\Controllers;

use App\Models\Kta;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use function Spatie\LaravelPdf\Support\pdf;

class KtaController extends Controller
{
    public function show(string $ktaNumber, Request $request)
    {
        $kta = Kta::query()
            ->with(['member.regionalLeader', 'member.village.district.city.province'])
            ->where('number', $ktaNumber)
            ->first();
        if(!$kta) {
            abort(404, 'Kartu Tanda Anggota tidak ditemukan.');
        }

        $member = $kta->member;

        // Pimpinan Daerah dulu; kalau belum diisi, jatuh ke provinsi domisili.
        $regionName = $member->regionalLeader?->name
            ?? $member->village?->district?->city?->province?->name;

        $bgKta = public_path('kta/BG-KTA.png');
        $bgKtaBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($bgKta));

        // Logo PMMBN
        $logoPath = public_path('assets/img/logo/pmmbn.png');
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

        // Poppins di-embed sebagai data URI: dompdf hanya menerima @font-face format('truetype'),
        // dan protokol data:// lolos tanpa perlu menyetel chroot (beda dengan file://).
        $poppinsRegularBase64 = 'data:font/ttf;base64,' . base64_encode(file_get_contents(public_path('fonts/poppins/Poppins-Regular.ttf')));
        $poppinsSemiBoldBase64 = 'data:font/ttf;base64,' . base64_encode(file_get_contents(public_path('fonts/poppins/Poppins-SemiBold.ttf')));
        $poppinsBoldBase64 = 'data:font/ttf;base64,' . base64_encode(file_get_contents(public_path('fonts/poppins/Poppins-Bold.ttf')));

        // Quiet zone QR dibuat lewat padding wrapper putih di view, bukan margin di sini.
        $qrCode = QrCode::format('svg')
            ->size(200)
            ->backgroundColor(255, 255, 255, 0)
            ->color(0, 0, 0)
            ->margin(0)
            ->generate(route('kta.show', ['ktaNumber' => $kta->number]));
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCode);

        if($request->input('type') == 'view') {
            return view('pdf.kta', compact('kta', 'regionName', 'bgKtaBase64', 'logoBase64', 'qrBase64', 'poppinsRegularBase64', 'poppinsSemiBoldBase64', 'poppinsBoldBase64') + ['isWebView' => true]);
        }

        return pdf()
            ->view('pdf.kta', compact('kta', 'regionName', 'bgKtaBase64', 'logoBase64', 'qrBase64', 'poppinsRegularBase64', 'poppinsSemiBoldBase64', 'poppinsBoldBase64'))
            ->margins(0, 0, 0, 0, 'mm')
            ->paperSize(85.6 * 2, 53.98 * 2, 'mm')
            ->name('kta-'.$kta->number.'.pdf');
    }
}
