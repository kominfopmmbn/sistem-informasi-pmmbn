<?php

namespace App\Models;

use App\Enums\MemberActivationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Mattiverse\Userstamps\Traits\Userstamps;

#[Fillable(['member_activation_id', 'status_id', 'notes'])]
class MemberActivationStatusLog extends Model
{
    use Userstamps;

    protected $table = 'member_activation_status_logs';

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        $status = MemberActivationStatus::tryFrom($this->status_id);
        if(!$status) {
            return '—';
        }
        return '<span class="badge '.$status->badgeClass().'">'.$status->label().'</span>';
    }

    public function isAccepted(): bool
    {
        return $this->status_id == MemberActivationStatus::VERIFIED->value;
    }

    public function isRejected(): bool
    {
        return $this->status_id == MemberActivationStatus::REJECTED->value;
    }

    public function isPending(): bool
    {
        return $this->status_id == MemberActivationStatus::PENDING->value;
    }
}
