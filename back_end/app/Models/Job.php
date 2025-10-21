<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Application;


class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'company_name',
        'location',
        'employment_type',
        'salary',
        'posted_by',
    ];

    public function employer()
{
    return $this->belongsTo(User::class, 'posted_by');
}

public function applications(){
    return $this->hasMany(Application::class,'job_id');
}

}
