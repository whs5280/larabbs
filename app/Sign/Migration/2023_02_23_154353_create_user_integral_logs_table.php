<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserIntegralLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_integral_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->tinyInteger('integral_type')->comment('10签到;2连续签到;3补签');
            $table->integer('integral')->comment('积分,有正负之分');
            $table->string('desc')->nullable()->comment('描述');
            $table->timestamps();
        });

        \DB::statement("ALTER TABLE `user_integral_logs` comment '用户积分明细'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_integral_logs');
    }
}
