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

/*
| Laravel menulis manifest paket/penyedia layanan dan cache config/route/event
| ke bootstrap/cache/. Di Vercel direktori itu read-only, sehingga penulisan
| gagal saat bootstrap (PackageManifest / ProviderRepository melempar exception
| sebelum provider — termasuk "view" — sempat terdaftar, memicu
| "Target class [view] does not exist"). Arahkan seluruh path cache bootstrap ke
| /tmp yang writable. Env ini harus di-set SEBELUM Application dibuat karena
| getCachedPackagesPath() dibaca di constructor Application.
*/
$bootstrapCache = '/tmp/bootstrap/cache';
is_dir($bootstrapCache) || @mkdir($bootstrapCache, 0755, true);

foreach ([
    'APP_PACKAGES_CACHE' => $bootstrapCache.'/packages.php',
    'APP_SERVICES_CACHE' => $bootstrapCache.'/services.php',
    'APP_CONFIG_CACHE'   => $bootstrapCache.'/config.php',
    'APP_ROUTES_CACHE'   => $bootstrapCache.'/routes-v7.php',
    'APP_EVENTS_CACHE'   => $bootstrapCache.'/events.php',
] as $key => $path) {
    putenv("{$key}={$path}");
    $_ENV[$key] = $path;
    $_SERVER[$key] = $path;
}

require __DIR__.'/../public/index.php';
