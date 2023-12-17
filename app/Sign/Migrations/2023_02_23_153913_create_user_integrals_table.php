<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserIntegralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_integrals', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->unsignedInteger('integral')->comment('当前积分');
            $table->unsignedInteger('total_integral')->comment('累计积分');
            $table->timestamps();

            $table->index('user_id');
        });

        //\DB::statement("ALTER TABLE `'user_integrals` comment '用户积分'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_integrals');
    }
}
