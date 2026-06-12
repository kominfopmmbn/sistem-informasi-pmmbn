<?php

/*
|--------------------------------------------------------------------------
| Vercel serverless entry point
|--------------------------------------------------------------------------
|
| Vercel (vercel-php) menjalankan aplikasi sebagai serverless function dengan
| filesystem read-only kecuali direktori /tmp. Laravel butuh lokasi tulis untuk
| compiled views, cache framework, dan berkas temporer MediaLibrary, jadi kita
| siapkan kerangka storage di /tmp sebelum membootstrap aplikasi.
|
| Storage path diarahkan ke /tmp/storage lewat AppServiceProvider::register()
| (dipicu env VERCEL yang otomatis di-set Vercel).
|
*/

$storage = '/tmp/storage';

foreach ([
    $storage.'/app/public',
    $storage.'/framework/cache/data',
    $storage.'/framework/sessions',
    $storage.'/framework/views',
    $storage.'/media-library/temp',
    $storage.'/logs',
] as $directory) {
    is_dir($directory) || @mkdir($directory, 0755, true);
}

require __DIR__.'/../public/index.php';
