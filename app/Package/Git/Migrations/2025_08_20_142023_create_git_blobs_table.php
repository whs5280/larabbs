<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGitBlobsTable extends Migration
{
    /**
     * 内容快照表 (git_blobs)
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('git_blobs', function (Blueprint $table) {
            $table->id('blob_id');
            $table->integer('file_id')->comment('关联到`files`表，表示这个blob属于哪个文件');
            $table->string('content_hash', 40)->unique()->comment('文件内容的`SHA1`哈希值 (内容寻址的关键)');
            $table->longText('content')->comment('文件内容（可压缩后存储）');
            $table->integer('size')->comment('内容的原始大小');
            $table->index(['content_hash']);
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
        Schema::dropIfExists('git_blobs');
    }
}
