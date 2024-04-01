<?php

namespace App\ThirdParty\Service;

/**
 * 导出服务
 * 效率: 30w条接近18s
 * 参考: app/ThirdParty/Test/ExportServiceTest.php
 */
class ExportService
{
    /**
     * @param array $headerTitle
     * @param string $fileName
     * @param bool $convert
     */
    public function __construct(array $headerTitle, string $fileName = '', bool $convert = true)
    {
        if (!$fileName) { $fileName = date('YmdHis'); }

        set_time_limit(0);
        header("Content-type:text/csv;charset=utf-8");
        header("Content-Disposition:attachment;filename=".$fileName.".csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        ob_flush();
        if ($convert) {
            echo mb_convert_encoding($this->implodeAndFilter($headerTitle)."\n", 'GBK', 'utf-8');
        } else {
            echo $this->implodeAndFilter($headerTitle)."\n";
        }
    }

    /**
     * 输出一行数据
     * @param array $field
     * @param bool $convert
     * @return void
     */
    public function outputRow(array $field, bool $convert = true) {
        if ($convert) {
            echo mb_convert_encoding($this->implodeAndFilter($field)."\n", 'GBK', 'utf-8');
        } else {
            echo $this->implodeAndFilter($field)."\n";
        }
    }

    /**
     * 拼接数组并过滤非法CSV字符
     * @param array $field
     * @return string
     */
    protected function implodeAndFilter(array $field): string
    {
        return preg_replace(["/[,\"\r\n]+/i", "/(_!_)/i"], ['', ","] , implode("_!_", $field));
    }
}
