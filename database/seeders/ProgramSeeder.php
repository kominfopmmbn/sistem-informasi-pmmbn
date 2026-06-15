<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProgramSeeder extends Seeder
{
    /**
     * Isi tabel programs (program unggulan) beserta tujuan dan foto asli.
     *
     * Foto diunduh saat seeding dari URL Unsplash terkurasi via addMediaFromUrl();
     * bila jaringan tak tersedia, otomatis jatuh ke gambar placeholder agar
     * seeding tidak gagal total.
     */
    public function run(): void
    {
        foreach ($this->programs() as $i => $row) {
            $program = Program::updateOrCreate(
                ['slug' => Str::slug($row['title'])],
                [
                    'title' => $row['title'],
                    'slug' => Str::slug($row['title']),
                    'excerpt' => $row['excerpt'],
                    'about_heading' => $row['about_heading'],
                    'about_content' => $row['about_content'],
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );

            $this->syncGoals($program, $row['goals']);

            $this->attachRemoteImage($program, $row['cover'], Program::COVER_COLLECTION, "cover-{$program->id}.jpg");

            // Galeri: hanya isi bila kosong → idempoten & tidak mengunduh ulang tiap run.
            if ($program->getMedia(Program::GALLERY_COLLECTION)->isEmpty()) {
                foreach (array_values($row['gallery']) as $g => $url) {
                    $this->attachRemoteImage($program, $url, Program::GALLERY_COLLECTION, "gallery-{$program->id}-{$g}.jpg");
                }
            }
        }
    }

    /**
     * Sinkron daftar tujuan: hapus lalu sisip ulang dengan sort_order berurutan.
     * Meniru App\Http\Controllers\ProgramController::syncGoals().
     *
     * @param  array<int, string>  $goals
     */
    private function syncGoals(Program $program, array $goals): void
    {
        DB::transaction(function () use ($program, $goals): void {
            $program->goals()->delete();

            $order = 0;
            foreach ($goals as $content) {
                $content = trim($content);
                if ($content === '') {
                    continue;
                }

                $program->goals()->create([
                    'content' => $content,
                    'sort_order' => $order,
                ]);

                $order++;
            }
        });
    }

    /**
     * Lampirkan satu gambar dari URL remote; fallback ke placeholder bila gagal.
     */
    private function attachRemoteImage(Program $program, string $url, string $collection, string $name): void
    {
        // Cover bersifat singleFile → lewati bila sudah ada agar tak mengunduh ulang.
        if ($collection === Program::COVER_COLLECTION && $program->getFirstMedia($collection) !== null) {
            return;
        }

        try {
            $program->addMediaFromUrl($url)
                ->usingFileName($name)
                ->toMediaCollection($collection);
        } catch (\Throwable $e) {
            $this->command?->warn("Gagal mengunduh {$url} ({$e->getMessage()}). Memakai gambar placeholder.");
            $program->addMedia(UploadedFile::fake()->image($name, 1200, 800))
                ->toMediaCollection($collection);
        }
    }

    /**
     * Data 3 program unggulan bertema PMMBN. URL foto Unsplash sudah diverifikasi
     * mengembalikan HTTP 200 + image/jpeg.
     *
     * @return array<int, array<string, mixed>>
     */
    private function programs(): array
    {
        $cover = fn (string $id): string => "https://images.unsplash.com/{$id}?auto=format&fit=crop&w=1200&q=80";
        $gallery = fn (string $id): string => "https://images.unsplash.com/{$id}?auto=format&fit=crop&w=1000&q=80";

        return [
            [
                'title' => 'Sekolah Moderasi Beragama',
                'excerpt' => 'Kelas dan kajian interaktif yang membekali mahasiswa dengan cara pandang beragama yang moderat, toleran, dan cinta tanah air — menjadikan kampus sebagai ruang dialog yang sehat di tengah keberagaman.',
                'about_heading' => 'Apa itu Sekolah Moderasi Beragama?',
                'about_content' => '<p>Sekolah Moderasi Beragama adalah program pendidikan unggulan PMMBN yang mempertemukan mahasiswa lintas latar belakang dalam serangkaian kelas, diskusi, dan kajian tematik. Peserta diajak memahami prinsip <em>wasathiyah</em> (jalan tengah): seimbang, adil, dan menghargai perbedaan tanpa kehilangan jati diri.</p><p>Selama satu semester, peserta belajar bersama narasumber dari berbagai disiplin — agama, kebangsaan, dan sosial — lalu menutupnya dengan proyek kecil penerapan moderasi di lingkungan masing-masing.</p>',
                'goals' => [
                    'Memahami prinsip dasar moderasi beragama dan relevansinya dalam kehidupan kampus.',
                    'Mampu berdialog lintas perspektif secara konstruktif dan saling menghormati.',
                    'Mengenali serta menolak narasi ekstremisme dan intoleransi.',
                    'Menjadi agen moderasi yang menebarkan nilai toleransi di komunitasnya.',
                ],
                'cover' => $cover('photo-1523580494863-6f3031224c94'),
                'gallery' => [
                    $gallery('photo-1524178232363-1fb2b075b655'),
                    $gallery('photo-1531482615713-2afd69097998'),
                    $gallery('photo-1543269865-cbf427effbad'),
                ],
            ],
            [
                'title' => 'Kemah Kebangsaan & Bela Negara',
                'excerpt' => 'Pembinaan karakter di alam terbuka yang menumbuhkan wawasan kebangsaan, kepemimpinan, dan semangat bela negara — merangkai kebersamaan lintas latar belakang dalam satu tenda yang sama.',
                'about_heading' => 'Membangun Karakter Lewat Kemah Kebangsaan',
                'about_content' => '<p>Kemah Kebangsaan &amp; Bela Negara adalah program tahunan PMMBN yang membawa mahasiswa keluar dari ruang kelas menuju pengalaman langsung di alam terbuka. Melalui apel kebangsaan, diskusi malam, dan simulasi kepemimpinan, peserta menghayati nilai-nilai Pancasila secara nyata.</p><p>Kegiatan dirancang untuk memupuk disiplin, gotong royong, dan rasa cinta tanah air, sekaligus mempererat solidaritas antarpeserta yang datang dari beragam daerah, suku, dan keyakinan.</p>',
                'goals' => [
                    'Menguatkan pemahaman nilai Pancasila dan wawasan kebangsaan.',
                    'Menumbuhkan jiwa kepemimpinan, kedisiplinan, dan tanggung jawab.',
                    'Mempererat solidaritas dan kerja sama lintas latar belakang.',
                    'Menumbuhkan kesiapan untuk berkontribusi nyata bagi bangsa dan negara.',
                ],
                'cover' => $cover('photo-1504280390367-361c6d9f38f4'),
                'gallery' => [
                    $gallery('photo-1496545672447-f699b503d270'),
                    $gallery('photo-1517649763962-0c623066013b'),
                    $gallery('photo-1526976668912-1a811878dd37'),
                ],
            ],
            [
                'title' => 'Pengabdian Masyarakat',
                'excerpt' => 'Aksi nyata mahasiswa turun ke tengah masyarakat — berbagi ilmu, merawat toleransi, dan membangun kemitraan dengan komunitas agar nilai moderasi tumbuh dari akar rumput.',
                'about_heading' => 'Menebar Manfaat Lewat Pengabdian Masyarakat',
                'about_content' => '<p>Program Pengabdian Masyarakat mengajak mahasiswa PMMBN menerapkan ilmu dan nilai moderasi langsung di tengah komunitas. Mulai dari kelas literasi untuk anak, pendampingan UMKM, hingga kegiatan lintas iman, peserta belajar bahwa perubahan dimulai dari langkah kecil yang konsisten.</p><p>Dengan menggandeng komunitas lokal sebagai mitra, program ini menumbuhkan empati, kepekaan sosial, dan budaya toleransi yang lahir dari pengalaman bersama, bukan sekadar teori.</p>',
                'goals' => [
                    'Menerapkan ilmu dan keterampilan untuk menjawab kebutuhan masyarakat.',
                    'Menumbuhkan empati dan kepekaan terhadap isu sosial di sekitar.',
                    'Merawat toleransi dan kerukunan di tingkat akar rumput.',
                    'Menjalin kemitraan berkelanjutan dengan komunitas lokal.',
                ],
                'cover' => $cover('photo-1593113598332-cd288d649433'),
                'gallery' => [
                    $gallery('photo-1488521787991-ed7bbaae773c'),
                    $gallery('photo-1542810634-71277d95dcbb'),
                    $gallery('photo-1469571486292-0ba58a3f068b'),
                ],
            ],
        ];
    }
}
