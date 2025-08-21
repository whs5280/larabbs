<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGitCommitsTable extends Migration
{
    /**
     * 提交记录表 (git_commits)
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('git_commits', function (Blueprint $table) {
            $table->id();
            $table->string('commit_hash', 40)->unique()->comment('提交的唯一哈希（可由父提交、作者、时间等信息计算）');
            $table->string('author', 100)->comment('提交者');
            $table->text('message')->comment('提交信息');
            $table->string('parent_commit', 40)->nullable()->comment('父提交的`commit_hash`，用于形成链式历史');
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
        Schema::dropIfExists('git_commits');
    }
}
