<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Job;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_id',
        'phone_number',
        'cover_letter',
        'resume_path',
    ];

    public function poster(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function job(){
        return $this->belongsTo(Job::class,'job_id');
    }
}
