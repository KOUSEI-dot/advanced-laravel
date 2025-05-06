<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreakTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('break_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_record_id'); // 勤怠レコードへの外部キー
            $table->timestamp('break_start')->nullable(); // 休憩開始
            $table->timestamp('break_end')->nullable();   // 休憩終了
            $table->timestamps();

            // 外部キー制約（削除されたら一緒に削除）
            $table->foreign('attendance_record_id')
                    ->references('id')
                    ->on('attendance_records')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('break_times');
    }
}
