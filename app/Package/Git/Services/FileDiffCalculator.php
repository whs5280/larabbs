<?php

namespace App\Package\Git\Services;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class FileDiffCalculator
{
    /**
     * 计算文件差异
     * 差异格式参考: https://github.com/sebastianbergmann/diff; 函数实现了 Myersdiff 算法
     *
     * @param string $textV1
     * @param string $textV2
     * @return string
     */
    public static function calculateLineDiff(string $textV1, string $textV2): string
    {
        $linesV1 = explode("\n", $textV1);
        $linesV2 = explode("\n", $textV2);

        $builder = new UnifiedDiffOutputBuilder(
            "--- Original\n+++ New\n", // custom header
            false              // do not add line numbers to the diff
        );
        $differ = new Differ($builder);

        return $differ->diff($linesV1, $linesV2);
    }
}
