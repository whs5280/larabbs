<?php

namespace App\Package\Git\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class GitFile extends Model
{
    use HasFactory;

    protected $table = 'git_files';

    public static function acquiredFileId($filePath)
    {
        $fileId = self::query()->where('file_path', $filePath)->value('file_id');
        if ($fileId) {
            return $fileId;
        }

        return self::query()->create([
            'file_path' => $filePath,
        ])->getKey();
    }

    public static function setLatestBlobIdAttr($fileId, $lastBlobId): int
    {
        return self::query()->where('file_id', $fileId)->update([
            'latest_blob_id' => $lastBlobId,
        ]);
    }
}
