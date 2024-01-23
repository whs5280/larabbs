<?php

namespace App\ThirdParty\Test;

use App\ThirdParty\Service\GaoDeiGeo;
use PHPUnit\Framework\TestCase;

class GaoDeiGeoTest extends TestCase
{
    public function testGetGeo()
    {
        GaoDeiGeo::getInstance()->getGeo('北京市朝阳区阜通东大街6号');
        $this->assertTrue(true);
    }

    public function testGetLocation()
    {
        GaoDeiGeo::getInstance()->getLocation('116.310003,39.991957');
        $this->assertTrue(true);
    }

    public function testGetDirection()
    {
        GaoDeiGeo::getInstance()->getDirection('116.481028,39.989643', '116.434446,39.90816');
        $this->assertTrue(true);
    }

    public function testGetDirectionV2()
    {
        GaoDeiGeo::getInstance()->getDirectionV2('116.481028,39.989643', '116.434446,39.90816');
        $this->assertTrue(true);
    }

    public function testGetIp()
    {
        GaoDeiGeo::getInstance()->getIp('47.66.99.56');
        $this->assertTrue(true);
    }

    public function testGetConvert()
    {
        GaoDeiGeo::getInstance()->coordinateConvert('116.310003,39.991957');
        $this->assertTrue(true);
    }

    public function testGetWeatherInfo()
    {
        GaoDeiGeo::getInstance()->getWeather('110000');
        $this->assertTrue(true);
    }

    public function testGetInputTips()
    {
        GaoDeiGeo::getInstance()->getInputTips('招行', '440100');
        $this->assertTrue(true);
    }
}
