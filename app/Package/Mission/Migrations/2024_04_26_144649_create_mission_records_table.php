<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissionRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('group_id')->index()->comment('任务组ID');
            $table->unsignedInteger('acceptable_id')->index()->comment('任务接受者ID');
            $table->string('acceptable_type')->nullable()->comment('任务接受者ID');
            $table->unsignedInteger('mission_id')->index()->comment('任务ID');
            $table->string('reward_type', 128)->nullable()->comment('奖励类型');
            $table->integer('reward_count')->default(0)->comment('奖励数量');
            $table->tinyInteger('status')->nullable()->comment('状态');
            $table->timestamps();

            $table->index(['acceptable_id', 'acceptable_type']);
            $table->index(['acceptable_id', 'acceptable_type', 'mission_id', 'status'], 'acceptable_mission_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mission_records');
    }
}
