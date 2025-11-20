<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
    ];

    protected $casts = [
        'date'      => 'date',
        'clock_in'  => 'datetime:H:i',
        'clock_out' => 'datetime:H:i',
    ];

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class, 'attendance_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

