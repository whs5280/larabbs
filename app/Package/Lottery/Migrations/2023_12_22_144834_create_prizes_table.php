<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('l_prizes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->comment('奖品名称');
            $table->string('image', 100)->comment('奖品图片');
            $table->string('type', 30)->comment('奖品类型');
            $table->integer('total')->default(0)->comment('奖品总数');
            $table->integer('stock')->default(0)->comment('当前库存');
            $table->integer('probability')->default(0)->comment('中奖概率');
            $table->integer('order')->default(100)->comment('排序');
            $table->boolean('is_show')->default(true)->comment('状态');
            $table->string('remark', 100)->nullable()->comment('备注');
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
        Schema::dropIfExists('l_prizes');
    }
}
