<?php

namespace App\ThirdParty\Service;

use DOMXPath;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class NovelService
{
    /**
     * 小说目录[凡人修仙传]
     * @var string
     */
    private static $menuUrl = "http://www.xliangyusheng.com/33/33153";

    /**
     * @var bool 是否写入本地文件
     */
    private static $isWriteFile = true;

    /**
     * @var bool 是否切分换行
     */
    private static $isSplit = true;

    /**
     * @var int 默认切分长度；居左居中 (GBK 2; UFT-8 3)
     */
    private static $splitLength = 60;

    /**
     * @var int 行数字节切分长度；居右
     */
    private static $rightSplitLength = 18;

    /**
     * @var string Terminal呈现方式；默认左对齐方式；left | center | right
     */
    private static $align = 'left';

    /**
     * 保存章节内容
     * @param string $url
     * @return void
     */
    public static function saveChapterHtml(string $url = "http://www.xliangyusheng.com/33/33153/16078062.html")
    {
        $data = file_get_contents($url);
        $file = fopen("chapter.txt", "w") or die("无法打开文件");
        fwrite($file, $data);
        fclose($file);
    }

    /**
     * 获取章节目录
     * @return void
     * @throws GuzzleException
     */
    public static function acquireCatalogueTextOnline()
    {
        $client = new Client(["verify" => false]);
        $html = $client->get(self::$menuUrl)->getBody()->getContents();

        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $xpath = new DOMXPath($dom);
        $links = $xpath->query('(//div[@id="list"]//dd/a)');
        echo "Chapter Total: " . count($links);

        $resultStr = "";
        foreach ($links as $link) {
            $resultStr .= sprintf("%s%4s%s\n", trim($link->textContent), '', $link->getAttribute('href'));
        }

        $file = fopen("catalogue.txt", "w") or die("无法打开文件");
        fwrite($file, $resultStr);
        fclose($file);
    }

    /**
     * 获取章节内容
     * @param string $url catalogue.txt 获取
     * @param bool $isReversed 是否反转
     * @throws GuzzleException
     * @throws \Exception
     */
    public static function acquireChapterTextOnline(string $url = "http://www.xliangyusheng.com/33/33153/16078062.html", bool $isReversed = false)
    {
        $client = new Client(["verify" => false, 'timeout' => 5.0]);
        $html = $client->get($url)->getBody()->getContents();

        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $xpath = new DOMXPath($dom);
        $root = $xpath->query('//div[@id="content"]')->item(0);
        $text = $root->textContent;

        $title = $xpath->query('//title')->item(0)->nodeValue;

        $text = preg_replace('/\s+/u', '<br>', $text);
        $text = str_replace('&nbsp;', ' ', $text);
        $text = trim($text);
        $text = preg_replace('/\s*,\s*/u', ', ', $text);
        $text = array_filter(explode("<br>", $text));
        $isReversed == true && $text = array_reverse($text);

        $output = "{$title}\n";
        foreach ($text as $item) {
            if (strpos($item, GoldPwdService::decrypt('WU1RSHhIR20vRTRaVHVldC9wd3NIMHNLci9FVGRXWGJuaTc1RVo0SFVNND0=')) !== false
                || strpos($item, GoldPwdService::decrypt('aUsxVUhZakpaSWprRE4vSUdFNnE5QT09')) !== false
                || strpos($item, GoldPwdService::decrypt('bDZTVGR5Skd6ZERLNmpVV3JGZWgxUT09')) !== false
                || strpos($item, GoldPwdService::decrypt('UWsraHdHOWhEKzgySnVxblVDbVd3UT09')) !== false
            ) {
                continue;
            }

            if (self::$isSplit) {
                $itemFormat = self::intervalFormat($item, self::$align);
                $output .= sprintf("%s\n", $itemFormat);
            } else {
                $output .= sprintf("%4s%s\n\n",'', $item);  // %4s 缩进4个空格
            }
        }

        cache()->put(sprintf("novel:%s", $url), $output, 15 * 60);

        if (self::$isWriteFile) {
            preg_match('/\s第([\x{4e00}-\x{9fa5}]+)章\s/u', $title, $matches);    // utf-8 编码
            //preg_match('/\s第([\x80-\xff]+)章\s/', $title, $matches);             // gbk 编码
            $numerical = self::chineseToNumber($matches[1]);
            if (!file_exists('novel')) {
                mkdir('novel', 0777, true);
            }
            $file = fopen("novel/chapter_{$numerical}.md", "w") or die("无法打开文件");
            fwrite($file, sprintf("%s\n%s\n%s", "```", $output, "```"));
            fclose($file);
        }

        self::readChapterTextByCache($url);
    }

    /**
     * 读取章节内容；控制台乱码可能是编码问题，打开注释试试
     * @param $url
     * @return void
     * @throws \Exception
     */
    public static function readChapterTextByCache($url)
    {
        $text = cache()->get(sprintf("novel:%s", $url));
        // $text = mb_convert_encoding($text, 'GBK', 'UTF-8');
        echo ($text);
        die();
    }

    /**
     * 间隔格式化
     * @param $str
     * @param string $align
     * @return string
     */
    private static function intervalFormat($str, string $align = 'left'): string
    {
        $format = "%4s%s\n";
        $align == 'center' && $format = "%50s%s\n";
        $align == 'right'  && $format = "%170s%s";
        $align == 'right'  && self::$splitLength = self::$rightSplitLength;

        $strArrStr = '';
        $strArr = mb_str_split($str, self::$splitLength, 'UTF-8');
        foreach ($strArr as $item) {
            $strArrStr .= sprintf($format, '', $item);
        }
        return $align == 'right' ? $strArrStr . "\n" : $strArrStr;
    }

    /**
     * 中文转阿拉伯数字
     * @param $str
     * @return float|int
     */
    private static function chineseToNumber($str) {
        $map = [
            '零' => 0, '一' => 1, '二' => 2, '三' => 3, '四' => 4,
            '五' => 5, '六' => 6, '七' => 7, '八' => 8, '九' => 9,
            '十' => 10, '百' => 100, '千' => 1000, '万' => 10000
        ];

        $result = 0;
        $temp = 0;

        for ($i = 0; $i < mb_strlen($str); $i++) {
            $char = mb_substr($str, $i, 1);

            if (isset($map[$char])) {
                $value = $map[$char];

                if ($value >= 10) {
                    if ($temp == 0) $temp = 1;
                    $result += $temp * $value;
                    $temp = 0;
                } else {
                    $temp = $value;
                }
            }
        }

        return $result + $temp;
    }
}
