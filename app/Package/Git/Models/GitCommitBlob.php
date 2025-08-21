<?php

namespace App\Package\Git\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class GitCommitBlob extends Model
{
    use HasFactory;

    protected $table = 'git_commit_blobs';

    public static function createCommitBlob($commitHash, $blobId, $fileId)
    {
        return self::query()->create([
            'commit_hash' => $commitHash,
            'blob_id'     => $blobId,
            'file_id'     => $fileId,
        ]);
    }

    public static function getBlobsByCommitHash(...$commitHash): array
    {
        $commitBlobs = self::query()->whereIn('commit_hash', $commitHash)->get()->toArray();
        return array_column($commitBlobs, 'blob_id');
    }
}
