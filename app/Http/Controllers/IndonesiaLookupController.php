<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

/** Referensi wilayah Laravolt untuk select (admin dan publik); read-only JSON, tanpa data sensitif. */
class IndonesiaLookupController extends Controller
{
    /**
     * Kota/kabupaten per provinsi untuk UI select (format Select2-compatible: results + pagination.more).
     */
    public function cities(Request $request): JsonResponse
    {
        $request->merge([
            'term' => $request->input('term', $request->input('q')),
        ]);

        $validator = Validator::make($request->all(), [
            'province_id' => ['required', 'integer', 'exists:indonesia_provinces,id'],
            'term' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        /** @var Province $province */
        $province = Province::query()->findOrFail((int) $request->input('province_id'));
        $term = $request->input('term');
        $perPage = 20;
        $page = max(1, (int) $request->input('page', 1));

        $query = City::query()
            ->where('province_code', $province->code)
            ->orderBy('name');

        if ($term !== null && $term !== '') {
            $term = (string) $term;
            $query->where('name', 'like', '%'.$term.'%');
        }

        $cities = $query->paginate($perPage, ['*'], 'page', $page);

        $results = $cities->getCollection()->map(fn (City $c) => [
            'id' => $c->getKey(),
            'text' => $c->name,
        ])->values()->all();

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $cities->hasMorePages(),
            ],
        ]);
    }
}
