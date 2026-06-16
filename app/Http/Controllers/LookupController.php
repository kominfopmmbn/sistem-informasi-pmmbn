<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\College;
use App\Models\Village as AppVillage;
use Illuminate\Support\Facades\DB;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;

/** Referensi wilayah Laravolt untuk select (admin dan publik); read-only JSON, tanpa data sensitif. */
class LookupController extends Controller
{
    /**
     * Kota/kabupaten untuk UI select (format Select2-compatible: results + pagination.more).
     * `province_code` (kode BPS 2 digit) opsional: bila diisi, hasil disaring per provinsi; bila kosong, semua kota.
     */
    public function cities(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'province_code' => ['nullable', 'string', 'size:2', 'exists:provinces,code'],
            'q' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $q = $request->input('q');
        $perPage = 20;
        $page = max(1, (int) $request->input('page', 1));

        $query = City::query()->orderBy('name');

        $provinceCode = $request->input('province_code');
        if ($provinceCode !== null && $provinceCode !== '') {
            $query->where('province_code', $provinceCode);
        }

        if ($q !== null && $q !== '') {
            $q = (string) $q;
            $query->where(DB::raw('LOWER(name)'), 'like', '%'.strtolower($q).'%');
        }

        $cities = $query->paginate($perPage, ['*'], 'page', $page);

        $results = $cities->getCollection()->map(fn (City $c) => [
            'id' => $c->getKey(),
            'text' => $c->name,
            'code' => $c->code,
        ])->values()->all();

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $cities->hasMorePages(),
            ],
        ]);
    }

    /**
     * Perguruan tinggi untuk UI select (format Select2-compatible: results + pagination.more).
     */
    public function colleges(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'with_other' => ['nullable'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $q = $request->input('q');
        $perPage = 20;
        $page = max(1, (int) $request->input('page', 1));

        $query = College::query()
            ->orderBy('name');

        if ($q !== null && $q !== '') {
            $q = (string) $q;
            $query->where(DB::raw('LOWER(name)'), 'like', '%'.strtolower($q).'%');
        }

        $colleges = $query->paginate($perPage, ['*'], 'page', $page);

        $results = $colleges->getCollection()->map(fn (College $college) => [
            'id' => $college->getKey(),
            'text' => $college->name,
        ])->values()->all();

        // Opsi "Lainnya" hanya untuk form publik (?with_other=1); disematkan di atas halaman pertama.
        if ($request->boolean('with_other') && $page === 1) {
            array_unshift($results, ['id' => 'other', 'text' => 'Lainnya (tidak ada di daftar)']);
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $colleges->hasMorePages(),
            ],
        ]);
    }

    /**
     * Kecamatan per kota (wajib `city_code`); Select2-compatible.
     */
    public function districts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'city_code' => ['required', 'string', 'size:4', 'exists:cities,code'],
            'q' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $q = $request->input('q');
        $perPage = 20;
        $page = max(1, (int) $request->input('page', 1));
        $cityCode = (string) $request->input('city_code');

        $query = District::query()
            ->where('city_code', $cityCode)
            ->orderBy('name');

        if ($q !== null && $q !== '') {
            $q = (string) $q;
            $query->where('name', 'like', '%'.$q.'%');
        }

        $districts = $query->paginate($perPage, ['*'], 'page', $page);

        $results = $districts->getCollection()->map(fn (District $d) => [
            'id' => $d->code,
            'text' => $d->name,
            'code' => $d->code,
        ])->values()->all();

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $districts->hasMorePages(),
            ],
        ]);
    }

    /**
     * Desa/kelurahan per kecamatan (wajib `district_code`); Select2-compatible.
     */
    public function villages(Request $request): JsonResponse
    {
        // GET + TrimStrings bisa membuat kode bertipe CHAR berubah panjang antar mesin DB; pakai bentuk kanonikal dari kolom districts.code.
        $districtInput = $request->input('district_code');
        if (is_string($districtInput)) {
            $normalized = preg_replace('/\s+/', '', $districtInput);
            if ($normalized !== '' && ctype_digit($normalized)) {
                $districtRow = District::query()
                    ->whereRaw('trim(code) = ?', [$normalized])
                    ->first();
                if ($districtRow !== null) {
                    $request->merge(['district_code' => $districtRow->code]);
                }
            }
        }

        $validator = Validator::make($request->all(), [
            // Tanpa length ketat: format kolom bisa bervariasi antar SQLite/PostgreSQL; pemadanan di atas dan `exists` sudah mencukupi.
            'district_code' => ['required', 'string', 'max:14', 'exists:districts,code'],
            'q' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $q = $request->input('q');
        $perPage = 20;
        $page = max(1, (int) $request->input('page', 1));
        $districtCode = (string) $request->input('district_code');

        $query = Village::query()
            ->where('district_code', $districtCode)
            ->orderBy('name');

        if ($q !== null && $q !== '') {
            $q = (string) $q;
            $query->where('name', 'like', '%'.$q.'%');
        }

        $villages = $query->paginate($perPage, ['*'], 'page', $page);

        $results = $villages->getCollection()->map(fn (Village $v) => [
            'id' => $v->code,
            'text' => $v->name,
            'code' => $v->code,
        ])->values()->all();

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $villages->hasMorePages(),
            ],
        ]);
    }

    /**
     * Desa/kelurahan untuk select domisili — pencarian rata (tanpa kecamatan induk), berdasarkan nama ATAU kode pos.
     * Kode pos tersimpan di dalam kolom `meta` JSON (key `pos`); `meta->pos` dikompilasi ke json_extract di MySQL & SQLite.
     */
    public function villagesSearch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $q = $request->input('q');
        $perPage = 20;
        $page = max(1, (int) $request->input('page', 1));

        $query = AppVillage::query()
            ->with('district.city.province')
            ->orderBy('name');

        if ($q !== null && $q !== '') {
            $q = (string) $q;
            $query->where(function ($w) use ($q): void {
                $w->where('name', 'like', '%'.$q.'%')
                    ->orWhere('meta->pos', 'like', '%'.$q.'%');
            });
        }

        $villages = $query->paginate($perPage, ['*'], 'page', $page);

        $results = $villages->getCollection()->map(fn (AppVillage $v) => [
            'id' => $v->code,
            'text' => $v->select_label,
            'code' => $v->code,
        ])->values()->all();

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $villages->hasMorePages(),
            ],
        ]);
    }
}
