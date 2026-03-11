<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'district_id',
        'name_ru',
        'name_kz'
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
