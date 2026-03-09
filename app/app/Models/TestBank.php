<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestBank extends Model
{
    protected $table = 'test_bank';

    protected $fillable = [
        'num',
        'subject',
        'grade',
        'lang',
        'variant',
        'type',
        'context',
        'question',
        'options',
        'correct_answer',
        'points',
        'img'
    ];
}
