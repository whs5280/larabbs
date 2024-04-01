<?php

namespace App\Handlers;

class CSVExportHandler
{
    /**
     * 获取路径
     *
     * @param $filename
     * @return string
     */
    private function getFilePath($filename): string
    {
        return storage_path() . "/exports/{$filename}.csv";
    }

    /**
     * 文件流写入文件
     *
     * @param $filename
     * @param $data
     * @param array $heads
     * @return void
     */
    public function store($filename, $data, array $heads = [])
    {
        $filepath = $this->getFilePath($filename);

        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        touch($filepath);
        $header = implode(",", $heads) . "\r\n";
        $csv = fopen($filepath, "a");
        fwrite($csv, $header);
        foreach ($data as $item) {
            $line = implode(',', $item) . "\r\n";
            fwrite($csv, $line);
        }
        fclose($csv);
    }

    /**
     * 文件下载
     *
     * @param $filename
     * @return void
     */
    public function download($filename)
    {
        try {
            $filepath = $this->getFilePath($filename);
            $fileSize = filesize($filepath);
            // 文件头
            header("Content-Disposition:attachment;filename=".$filename.'.csv');
            // header("Content-type:text/csv");
            header("Content-type: application/octet-stream;charset=utf-8");
            header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
            header('Expires:0');
            header('Pragma:public');
            header("Accept-Ranges: bytes");
            header("Accept-Length:" . $fileSize);

            $fp = fopen($filename, "r");
            $buffer = 1024;
            $fileCount = 0;
            //向浏览器返回数据
            while (!feof($fp) && $fileCount < $fileSize) {
                $fileCon = fread($fp, $buffer);
                $fileCount += $buffer;
                echo $fileCon;
            }
            fclose($fp);

        } catch (\Exception $exception) {
            logger()->channel('json')->error('CSV 导出失败', [
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'filename' => $filename,
            ]);
        }
    }
}
