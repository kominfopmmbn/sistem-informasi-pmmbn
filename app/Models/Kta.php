<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mattiverse\Userstamps\Traits\Userstamps;

#[Fillable(['member_id', 'order_number', 'number'])]
class Kta extends Model
{
    use Userstamps;

    protected $table = 'kta';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            try {
                $model->order_number = $model->generateOrderNumber($model);
                $model->number = $model->generateNumber($model);
            } catch (\Throwable $e) {
                Log::error('Kta creating failed: ' . $e->getMessage(), [
                    'model' => $model->toArray(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    public function generateNumber(self $model): string
    {
        $pwCode = $model->member?->orgRegion?->code ?? '00';
        $year = date('y');
        return $pwCode . $year . str_pad($model->order_number, 4, '0', STR_PAD_LEFT);
    }

    public function generateOrderNumber(self $model): int
    {
        $year = date('Y');
        $orderNumberByYear = DB::table('kta')->whereYear('created_at', '=', $year)->orderBy('order_number', 'desc')->first();
        return $orderNumberByYear ? $orderNumberByYear->order_number + 1 : 1;
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
