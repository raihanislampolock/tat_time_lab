<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TatTimeLab extends Model
{
    protected $table = "tat_time_lab";
    const CREATED_AT = 'cd';
    const UPDATED_AT = 'ud';

    public function TestType()
    {
        return $this->hasOne(TestType::class, 'id', 'b2b_b2c');
    }

}
