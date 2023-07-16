<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobsLang extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'language',
        'job_title',
        'job_company',
        'job_description',
        'job_qualifications',
        'job_offer',
    ];

    public function job(){
        return $this->hasOne(Jobs::class);
    }

    public function language(){
        return $this->belongsTo(Languages::class);
    }
}
