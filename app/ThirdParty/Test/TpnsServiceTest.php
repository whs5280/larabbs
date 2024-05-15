<?php

namespace App\ThirdParty\Test;

use App\ThirdParty\Service\TpnsService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class TpnsServiceTest extends BaseTestCase
{
    use CreatesApplication;

    public function testAndroidPush()
    {
        $title = '系统消息';
        $content = '今天的天气预计为晴天，最高气温为 25°C，最低气温为 15°C。风速较小，降水概率很低。明天预计将有阵雨，最高气温为 22°C，最低气温为 14°C。风速较大，降水概率为 60%。请注意出行安全，根据天气情况做好准备。';
        app(TpnsService::class)->message($title, $content)->users([2663084])->push();

        $this->assertTrue(true);
    }
}
