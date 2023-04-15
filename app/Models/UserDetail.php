<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDetail extends Model
{
    protected $table = 'user_detail';
    protected $primaryKey = 'id_user';
    public $incrementing = false;

    protected $fillable = [
        'id_user',
        'phone',
        'id_card_number',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
