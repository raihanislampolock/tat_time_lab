<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestType extends Model
{
    protected $table = "test_type";
    const CREATED_AT = 'cd';
    const UPDATED_AT = 'ud';

}