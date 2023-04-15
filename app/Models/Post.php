<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Post
 *
 * @property int $id_user
 * @property int $id_course
 *
 * @package App\Models
 */
class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $fillable = [
        'id_user',
        'title',
        'description',
        'content',
        'id_course',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'id_course');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'id_post');
    }
}
