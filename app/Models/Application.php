<?php

namespace App\Models;

use App\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'job_id',
        'jobseeker_id',
        'status',
    ];

    protected $casts = [
        'status' => ApplicationStatus::class,
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function jobseeker()
    {
        return $this->belongsTo(User::class, 'jobseeker_id');
    }

}
