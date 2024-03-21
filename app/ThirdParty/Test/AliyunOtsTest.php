<?php

namespace App\ThirdParty\Test;
use App\Models\OTS\AliyunOTS;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class AliyunOtsTest extends BaseTestCase
{
    use CreatesApplication;

    public function testCreateTable()
    {
        $ots = AliyunOTS::getInstance();
        $this->assertTrue($ots->createTable('feeds'));
    }

    public function testPutOneRow()
    {
        $ots = AliyunOTS::getInstance();
        $request = array (
            'table_name' => 'feeds',
            'condition' => AliyunOTS::CONST_IGNORE,
            'primary_key' => array (
                array('user_id', 10000),
                array('type_id', 202),
                array('data', 1009),
                array('created_at', time()),
            ),
            'defined_column' => array(
                array('status', 0)
            )
        );
        $this->assertTrue($ots->putOneRow($request));
    }

    public function testUpdateOneRow()
    {
        $ots = AliyunOTS::getInstance();
        $request = array (
            'table_name' => 'feeds',
            'condition' => AliyunOTS::CONST_IGNORE,
            'primary_key' => array (
                array('user_id', 10000),
                array('type_id', 202),
                array('data', 1009),
                array('created_at', time()),
            ),
            'update_of_attribute_columns' => array(
                'PUT' => array (
                    array('status', 1)
                ),
            )
        );
        $this->assertTrue($ots->updateOneRow($request));
    }
}
