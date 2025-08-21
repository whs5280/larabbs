<?php

namespace App\Package\Git\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class GitBlob extends Model
{
    use HasFactory;

    protected $table = 'git_blobs';

    public static function findBlobByHash($contentHash)
    {
        return self::query()->where('content_hash', $contentHash)->value('blob_id');
    }

    public static function createBlob($fileId, $contentHash, $content)
    {
        return self::query()->create([
            'file_id'      => $fileId,
            'content_hash' => $contentHash,
            'content'      => $content,
            'size'         => strlen($content),
        ])->getKey();
    }

    public static function getBlobContentById($blobId)
    {
        $blob = self::query()->where('blob_id', $blobId)->first();
        return $blob->content;
    }

    public function getContentAttribute($value): string
    {
        return gzuncompress(urldecode($value));
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = urlencode(gzcompress($value));
    }
}
