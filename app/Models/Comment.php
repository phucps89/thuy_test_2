<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Comment
 *
 * @property int $id_post
 * @property int $id_user
 *
 * @package App\Models
 */
class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';
    protected $fillable = [
        'id_parent',
        'id_user',
        'comment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'id_post');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'id_parent');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'id_parent');
    }
}
