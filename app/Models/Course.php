<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;
    protected $table = 'courses';
    protected $fillable = [
        'name',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(CourseRegistration::class, 'id_course');
    }

    public function posts(): HasMany {
        return $this->hasMany(Post::class, 'id_course');
    }
}
