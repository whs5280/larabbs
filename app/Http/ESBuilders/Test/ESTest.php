<?php

namespace App\Http\ESBuilders\Test;

use App\Http\ESBuilders\StoreInfoBuilder;
use App\Http\ESBuilders\TopicsBuilder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class ESTest extends BaseTestCase
{
    use CreatesApplication;

    public function testGroupBy()
    {
        $field = 'category_id';

        $builder = new TopicsBuilder();
        $result = $builder->groupBy($field)->search()->getResultAggs();

        $this->assertIsArray($result); // 验证结果是一个数组
    }

    public function testGeoDistanceQuery()
    {
        $coordinates = [-7.277583, 158.375339];
        $distance = '100km';

        $builder = new StoreInfoBuilder();
        $result = $builder->geoDistanceQuery($coordinates, $distance)->search()->getResultHits();

        $this->assertIsArray($result); // 验证结果是一个数组
        $this->assertGreaterThanOrEqual(0, count($result)); // 验证结果数组不为空
    }
}
