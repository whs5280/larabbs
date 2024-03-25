<?php

namespace App\ThirdParty\Test;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Log;
use Tests\CreatesApplication;

class LoggerTest extends BaseTestCase
{
    use CreatesApplication;

    public function testLog()
    {
        $request = ['userId' => rand(1, 100)];
        Log::channel('json')->info('数据插入成功', ['table' => 'users', 'request' => $request]);
        $this->assertTrue(true);
    }
}
