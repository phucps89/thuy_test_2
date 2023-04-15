<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 *
 * @property bool $is_admin
 * @property bool $is_instructor
 * @property bool $is_user
 * @property string $role
 * @property ?UserDetail $detail
 * @property Collection $courses
 *
 * @package App\Models
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'ADMIN';
    const ROLE_INSTRUCTOR = 'INSTRUCTOR';
    const ROLE_USER = 'USER';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'bool',
    ];

    public function detail(): HasOne
    {
        return $this->hasOne(UserDetail::class, 'id_user');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function posts(): HasMany {
        return $this->hasMany(Post::class, 'id_user');
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->role == self::ROLE_ADMIN;
    }

    public function getIsInstructorAttribute(): bool
    {
        return $this->role == self::ROLE_INSTRUCTOR;
    }

    public function getIsUserAttribute(): bool
    {
        return $this->role == self::ROLE_USER;
    }

    public function courses(): HasManyThrough
    {
        return $this->hasManyThrough(
            Course::class,
            CourseRegistration::class,
            'id_user',
            'id',
            null,
            'id_course'
        );
    }
}
