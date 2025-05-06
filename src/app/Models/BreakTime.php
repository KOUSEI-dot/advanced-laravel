<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_record_id',
        'break_start',
        'break_end',
    ];

    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function requests()
    {
        return $this->hasMany(BreakTimeRequest::class);
    }

    protected $casts = [
    'break_start' => 'datetime',
    'break_end' => 'datetime',
    ];

}
