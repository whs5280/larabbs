<?php

namespace App\Package\QueryCache\Test;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class QueryCacheTest extends BaseTestCase
{
    use CreatesApplication;

    public function testGet()
    {
        $topic = Topic::get();

        $topic02 = cache()->get('laravel_database_laravel_cache:DbQuery:mysql:get:select * from `topics`a:0:{}');

        $this->assertEquals($topic, $topic02);
    }
}
