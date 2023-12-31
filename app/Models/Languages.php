<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Languages extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        "language_code",
    ];

    public function users(){
        return $this->hasMany(User::class);
    }

    public function jobsLangs(){
        return $this->hasMany(JobSlang::class);
    }
}
