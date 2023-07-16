<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jobs extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        "active",
    ];

    public function jobLang(){
        return $this->belongsTo(JobsLang::class, 'id', 'job_id');
    }

    public function media(){
        return $this->morphToMany(Media::class, 'mediables');
    }

    public function tags(){
        return $this->morphToMany(Tag::class, 'tagables');
    }
}
