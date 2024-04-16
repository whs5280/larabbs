<?php

namespace App\Package\Collector\Test;

use App\Package\Collector\Adapter\JingDongItem;
use App\Package\Collector\Adapter\TaoBaoItem;
use App\Package\Collector\CollectorFactory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class FactoryTest extends BaseTestCase
{
    use CreatesApplication;

    public function testCreate()
    {
        $url = 'https://item.jd.com/10055892531578.html?sdx=ehi-lLxFuZiE6JnIZIZUhcQlszGUDw4rsmpPtadHYtuDPe_RLJ1V4XTnrUDnUmGT#crumb-wrap';
        $collector = CollectorFactory::create($url);
        $this->assertEquals(true, $collector instanceof JingDongItem);

        $url = 'https://detail.tmall.com/item.htm?abbucket=2&id=672030384045';
        $collector = CollectorFactory::create($url);
        $this->assertEquals(true, $collector instanceof TaoBaoItem);
    }
}
