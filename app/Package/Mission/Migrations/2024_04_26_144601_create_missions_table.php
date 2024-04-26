<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->bigIncrements('mission_id');
            $table->unsignedInteger('group_id')->index()->comment('任务组ID');
            $table->string('type')->nullable()->comment('任务类型');
            $table->string('name')->nullable()->comment('任务名称');
            $table->string('description')->nullable()->comment('任务描述');
            $table->string('link')->nullable()->comment('跳转链接');
            $table->string('handler')->nullable()->comment('处理方式');
            $table->string('period_type', 64)->nullable()->comment('周期类型');
            $table->string('reward_type', 128)->nullable()->comment('奖励类型');
            $table->integer('reward_count')->default(0)->comment('奖励次数');
            $table->string('qua_id')->nullable()->comment('资格ID');
            $table->tinyInteger('is_active')->default(0)->comment('是否上架');
            $table->smallInteger('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('missions');
    }
}
