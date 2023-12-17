<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSignLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_sign_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('month')->comment('年月');
            $table->string('bit_log', 31)->nullable()->comment('签到记录,0未1已签');
            $table->unsignedInteger('max_day')->default(0)->comment('连续签到最大天数');
            $table->timestamps();
        });

        //\DB::statement("ALTER TABLE `user_sign_logs` comment '用户签到明细'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_sign_logs');
    }
}
