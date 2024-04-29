<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissionShareRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_share_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('mission_id')->index()->comment('任务ID');
            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('用户ID');
            $table->unsignedBigInteger('sharer_id')->nullable()->index()->comment('分享者ID');
            $table->string('sharer_type')->nullable()->comment('分享者类型');
            $table->timestamps();

            $table->index(['sharer_id', 'sharer_type']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mission_share_records');
    }
}
