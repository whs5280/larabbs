<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrizeRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('l_prize_rewards', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户ID');
            $table->integer('prize_id')->comment('奖品ID');
            $table->timestamps();

            $table->index(['user_id', 'prize_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('l_prize_rewards');
    }
}
