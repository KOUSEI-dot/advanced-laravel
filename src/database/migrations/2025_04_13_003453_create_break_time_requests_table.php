<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreakTimeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('break_time_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('break_time_id')->nullable(); // 修正対象の休憩時間
            $table->unsignedBigInteger('user_id');
            $table->time('requested_break_start')->nullable();
            $table->time('requested_break_end')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->foreign('break_time_id')->references('id')->on('break_times')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('break_time_requests');
    }
}
