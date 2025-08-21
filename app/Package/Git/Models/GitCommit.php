<?php

namespace App\Package\Git\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class GitCommit extends Model
{
    use HasFactory;

    protected $table = 'git_commits';

    public static function createCommit($author, $message): string
    {
        $commitHash = sha1(vsprintf('%s_%s_%s', [$author, $message, time()]));
        self::query()->create([
            'commit_hash'   => $commitHash,
            'author'        => $author,
            'message'       => $message,
            'parent_commit' => null,    // 可用于分支链路追踪，暂未实现
        ]);
        return $commitHash;
    }
}
