<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'status', 'message'])]
class MemberActivationStatusLog extends Model
{
    protected $table = 'member_activation_status_logs';
}
