<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TatTimeLab extends Model
{
    protected $table = "tat_times.lab_tat_tat_time_lab";
    const CREATED_AT = 'cd';
    const UPDATED_AT = 'ud';

    protected $fillable = [
        'start_time',
        'end_time',
        'report_delivery',
    ];

    // public function setStartTimeAttribute($value)
    // {
    //     $this->attributes['start_time'] = Carbon::parse($value)->format('H:i');
    // }
    // public function setEndTimeAttribute($value)
    // {
    //     $this->attributes['end_time'] = Carbon::parse($value)->format('H:i');
    // }
    // public function setReportDeliveryAttribute($value)
    // {
    //     $this->attributes['report_delivery'] = Carbon::parse($value)->format('H:i');
    // }
}
