<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    protected $table = 'test_attempts';

    protected $fillable = [
        'student_name',
        'subject',
        'grade',
        'variant',
        'lang',
        'score',
        'started_at',
        'finished_at'
    ];


}
