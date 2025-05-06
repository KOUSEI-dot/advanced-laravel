<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // attendance_requests テーブル
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id();

            // ユーザーと勤怠データへの外部キー
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('attendance_id')->constrained('attendance_records')->onDelete('cascade');

            // 理由とステータスのみ保持
            $table->text('request_reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // 承認した管理者
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->time('requested_clock_in')->nullable();   // 修正された出勤時刻
            $table->time('requested_clock_out')->nullable();  // 修正された退勤時刻

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_requests');
    }
}
