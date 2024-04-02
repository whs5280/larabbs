<?php

namespace App\Support\Redis;

use Illuminate\Support\Facades\Redis;

class RedisRankBoard
{
    private $leaderboard;

    public function __construct($cacheKey)
    {
        $this->leaderboard = $cacheKey;
    }

    /**
     * 添加节点
     *
     * @param $node
     * @param int $value
     * @return mixed
     */
    public function add($node, int $value = 1)
    {
        $exist = Redis::zScore($this->leaderboard, $node);
        if ($exist) {
            return Redis::zAdd($this->leaderboard, $this->getNodeValue($node) + $this->calculateCount($value), $node);
        } else {
            return Redis::zAdd($this->leaderboard, $this->calculateCount($value), $node);
        }
    }

    /**
     * 排行榜列表
     * zRevRange 高分数排序; zRange 低分数排序
     *
     * @param $number
     * @param bool $asc
     * @param array $withScores
     * @return mixed
     */
    public function list($number, bool $asc = true, array $withScores = [])
    {
        $func = $asc ? 'zRevRange' : 'zRange';
        return Redis::$func($this->leaderboard, 0, $number - 1, $withScores);
    }

    /**
     * 获取给定节点的分数（取整）
     *
     * @param $node
     * @return int
     */
    public function getNodeValue($node): int
    {
        return intval(Redis::zScore($this->leaderboard, $node));
    }

    /**
     * 获取给定节点的排名
     *
     * @param $node
     * @param bool $asc
     * @return mixed
     */
    public function getNodeRank($node, bool $asc = true)
    {
        if ($asc) {
            // zRevRank 分数最高的排行为0,所以需要加1位
            return Redis::zRevRank($this->leaderboard, $node);
        } else {
            return Redis::zRank($this->leaderboard, $node);
        }
    }

    /**
     * 计算公式（带上时间戳）
     *
     * @param $val
     * @return float|int
     */
    public function calculateCount($val) {
        return $val +  (9999999999 - time()) / 100000000000;
    }
}
