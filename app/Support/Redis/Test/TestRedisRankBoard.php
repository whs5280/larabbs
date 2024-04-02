<?php

namespace App\Support\Redis\Test;

use App\Support\Redis\RedisRankBoard;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class TestRedisRankBoard extends BaseTestCase
{
    use CreatesApplication;

    public function testRank()
    {
        $cacheKey = sprintf('%s_%d', 'test_rank', Carbon::now()->format('Ym'));

        $redis = new RedisRankBoard($cacheKey);
        $redis->add(10086, 450);
        $redis->add(10087, 400);
        sleep(2);
        $redis->add(10088, 400);
        $redis->add(10089, 450);

        $this->assertSame($redis->list(10), ['10086', '10089', '10087', '10088']);
    }
}
