<?php

namespace App\Package\Git\Services;

use App\Package\Git\Models\GitBlob;
use App\Package\Git\Models\GitCommit;
use App\Package\Git\Models\GitCommitBlob;
use App\Package\Git\Models\GitFile;

class VersionControl
{
    /**
     * 提交文件；目前只支持单个文件提交；多文件提交自行封装
     *
     * @param $filePath
     * @param $content
     * @param $author
     * @param $message
     * @return string
     * @throws \Throwable
     */
    public function commitFile($filePath, $content, $author, $message): string
    {
        $contentHash = sha1($content);

        $fileId = GitFile::acquiredFileId($filePath);
        throw_if(!$fileId, 'file not found');

        $blobId = GitBlob::findBlobByHash($contentHash);
        if (!$blobId) {
            $blobId = GitBlob::createBlob($fileId, $contentHash, $content);
        }

        $commitHash = GitCommit::createCommit($author, $message);
        GitCommitBlob::createCommitBlob($commitHash, $blobId, $fileId);
        GitFile::setLatestBlobIdAttr($fileId, $blobId);

        return $commitHash;
    }

    /**
     * 获取两个版本之间的差异
     *
     * @param $commitHashV1
     * @param $commitHashV2
     * @return string
     */
    public function getDiff($commitHashV1, $commitHashV2): string
    {
        list($BlobIdV1, $BlobIdV2) = GitCommitBlob::getBlobsByCommitHash($commitHashV1, $commitHashV2);
        $boldContentV1 = GitBlob::getBlobContentById($BlobIdV1);
        $boldContentV2 = GitBlob::getBlobContentById($BlobIdV2);

        return FileDiffCalculator::calculateLineDiff($boldContentV1, $boldContentV2);
    }
}
