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
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id();

            // --- 外部キー ---
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('attendance_id')
                ->constrained('attendance_records')
                ->onDelete('cascade');

            // --- 修正申請内容 ---
            $table->time('requested_clock_in')->nullable();   // 修正出勤時刻（任意）
            $table->time('requested_clock_out')->nullable();  // 修正退勤時刻（任意）

            // 休憩修正（複数可）→ JSON 形式
            // 例: [{"start": "12:00", "end": "12:30"}, {"start": "15:00", "end": "15:15"}]
            $table->json('requested_breaks')->nullable();

            // --- 理由 / ステータス ---
            $table->text('request_reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            // 承認した管理者
            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamps();
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
