<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissionGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_groups', function (Blueprint $table) {
            $table->bigIncrements('group_id');
            $table->string('tag', 128)->index()->comment('任务组标签');
            $table->string('name')->nullable()->comment('任务组名称');
            $table->dateTime('expired_at')->nullable()->comment('过期时间');
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
        Schema::dropIfExists('mission_groups');
    }
}
