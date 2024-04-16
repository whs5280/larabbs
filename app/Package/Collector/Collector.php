<?php

namespace App\Package\Collector;

/**
 * 适配器
 */
abstract class Collector
{
    protected $targetUrl;

    protected $resultHtml;

    protected $retryNum = 1;

    /**
     * 重试次数
     *
     * @param int $num
     * @return void
     */
    public function setRetryNum(int $num = 1)
    {
        $this->retryNum = $num;
    }

    /**
     * 采集
     *
     * @return mixed
     */
    public function collect() {
        return $this->formatResult($this->parseContent());
    }

    /**
     * 解析内容
     *
     * @return mixed
     */
    public function parseContent()
    {
        $itemId = $this->getItemId($this->targetUrl);
        $this->resultHtml = $this->getItemDetail($itemId);
        if (!$this->resultHtml) {
            $this->retryGrab($itemId);
        }
        return $this->resultHtml;
    }

    /**
     * 重试抓取
     *
     * @param $itemId
     * @return void
     */
    public function retryGrab($itemId)
    {
        $retryNum = 1;
        while ($retryNum <= $this->retryNum){
            $this->resultHtml = $this->getItemDetail($itemId);
            if($this->resultHtml){
                return;
            }
            $retryNum ++;
        }
    }

    abstract protected function getItemId($targetUrl);

    abstract protected function getItemDetail($itemId);

    abstract protected function formatResult($result);
}
