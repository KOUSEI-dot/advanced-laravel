<?php

namespace Database\Factories;

use App\Models\AttendanceRequest;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AttendanceRequestFactory extends Factory
{
    /**
     * The model that this factory corresponds to.
     *
     * @var string
     */
    protected $model = AttendanceRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = Carbon::today();
        $clockIn = $date->copy()->subHours(9)->format('H:i');
        $clockOut = $date->copy()->addHour()->format('H:i');

        return [
            'user_id'             => User::factory(),
            'attendance_id'       => AttendanceRecord::factory(),
            'requested_clock_in'  => $clockIn,
            'requested_clock_out' => $clockOut,
            'request_reason'      => $this->faker->sentence,
            'status'              => 'pending',
        ];
    }
}
