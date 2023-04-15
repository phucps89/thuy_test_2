<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseRegistration extends Model
{
    use HasFactory;
    public $incrementing = false;

    protected $table = 'course_registrations';
    protected $fillable = [
        'id_course',
        'id_user',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'id_course');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
