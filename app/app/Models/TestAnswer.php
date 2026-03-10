<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAnswer extends Model
{
    protected $table = 'test_answers';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'student_answer',
        'points_awarded'
    ];


    public function answers()
        {
            return $this->hasMany(TestAnswer::class,'attempt_id');
        }

    public function attempt()
    {
        return $this->belongsTo(TestAttempt::class,'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(TestBank::class,'question_id');
    }        
}
