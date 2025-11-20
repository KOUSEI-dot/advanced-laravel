<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceRecordFactory extends Factory
{
    protected $model = AttendanceRecord::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(), // ユーザーファクトリを参照
            'date' => $this->faker->date(),
            'clock_in' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'clock_out' => $this->faker->dateTimeBetween('now', '+1 week'),
            'status' => '勤務済み',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
