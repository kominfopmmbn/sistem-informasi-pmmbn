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
        $kta = Kta::query()->with(['member'])->where('number', $ktaNumber)->first();
        if(!$kta) {
            abort(404, 'Kartu Tanda Anggota tidak ditemukan.');
        }

        $member = $kta->member;

        $bgKta = public_path('kta/bg-kta.jpeg');
        $bgKtaBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($bgKta));

        // Logo PMMBN
        $logoPath = public_path('assets/img/logo/pmmbn.png');
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

        $qrCode = QrCode::format('svg')
            ->size(400)
            ->backgroundColor(255, 255, 255, 0)
            ->color(255, 255, 255)
            ->margin(2.5)
            ->generate(route('kta.show', ['ktaNumber' => $kta->number]));
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCode);

        if($request->input('type') == 'view') {
            return view('pdf.kta', compact('kta', 'bgKtaBase64', 'logoBase64', 'qrBase64'));
        }

        return pdf()
            ->view('pdf.kta', compact('kta', 'bgKtaBase64', 'logoBase64', 'qrBase64'))
            ->margins(0, 0, 0, 0, 'mm')
            ->paperSize(85.6 * 2, 53.98 * 2, 'mm')
            ->name('kta-'.$kta->number.'.pdf');
    }
}
