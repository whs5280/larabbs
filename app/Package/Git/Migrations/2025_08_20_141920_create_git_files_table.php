<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGitFilesTable extends Migration
{
    /**
     * 文件表 (git_files)
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('git_files', function (Blueprint $table) {
            $table->id('file_id');
            $table->string('file_path')->comment('文件的路径（如 src/utils.php），唯一标识一个文件');
            $table->integer('latest_blob_id')->nullable()->comment('指向该文件最新版本的blob_id');
            $table->index(['file_path']);
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
        Schema::dropIfExists('git_files');
    }
}
