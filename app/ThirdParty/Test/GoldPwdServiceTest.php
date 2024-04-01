<?php

namespace App\ThirdParty\Test;

use App\ThirdParty\Service\GoldPwdService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class GoldPwdServiceTest extends BaseTestCase
{
    use CreatesApplication;

    public function testEncrypt()
    {
        $data = ['userId' => 10086, 'token' => '123456'];
        $encrypt = GoldPwdService::encrypt($data);
        $decrypt = GoldPwdService::decrypt($encrypt, true);

        $this->assertEquals($data, $decrypt);
    }
}
