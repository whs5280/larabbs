<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGitCommitBlobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('git_commit_blobs', function (Blueprint $table) {
            $table->id();
            $table->string('commit_hash', 40)->comment('commit hash');
            $table->integer('blob_id')->comment('blob_id');
            $table->integer('file_id')->comment('file_id');
            $table->index(['commit_hash']);
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
        Schema::dropIfExists('git_commit_blobs');
    }
}
