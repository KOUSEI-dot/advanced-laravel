<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreakTimesTable extends Migration
{
    public function up()
    {
        Schema::create('break_times', function (Blueprint $table) {
            $table->id();

            // attendance_records.id と関連
            $table->unsignedBigInteger('attendance_id');

            // 時刻は start_time / end_time に統一
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->timestamps();

            $table->foreign('attendance_id')
                ->references('id')
                ->on('attendance_records')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('break_times');
    }
}
