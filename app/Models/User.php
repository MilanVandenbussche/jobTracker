<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, softDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'admin',
        'language',
        'email',
        'media_id',
        'password',
        'deleted_at',
    ];

    public function favorites(){
        return $this->belongsToMany(User::class, 'favorites', 'parent_id', 'child_id');
    }

    public function media(){
        return $this->belongsTo(Media::class);
    }

    public function tags(){
        return $this->morphToMany(Tag::class, 'tagables');
    }

    public function language(){
        return $this->belongsTo(Languages::class);
    }

    public function profilePicture($size = 'xs'): string
    {
        return $this->media_id ? $this->media->getPath($size) : $this->defaultProfilePic();
    }

    public function defaultProfilePic(): string
    {
        $name = $this->first_name . " " . $this->last_name;

        $bgColor = $this->deleted_at ? "dc3545" : "0d6efd";

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=f5f0ea&background=' . $bgColor;
    }

    public static function findByEmail($email)
    {
        return count(User::where('email', $email)->get()) !== 0;
    }

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
        'password' => 'hashed',
    ];
}
