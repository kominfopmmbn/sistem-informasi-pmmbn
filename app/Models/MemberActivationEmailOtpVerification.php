<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

#[Fillable(['email', 'otp', 'verified_at'])]
class MemberActivationEmailOtpVerification extends Model
{
    use Notifiable;

    public function memberActivation(): BelongsTo
    {
        return $this->belongsTo(MemberActivation::class);
    }
}
