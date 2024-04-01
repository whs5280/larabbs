<?php

namespace App\ThirdParty\Test;

use App\Models\Topic;
use App\ThirdParty\Service\ExportService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class ExportServiceTest extends BaseTestCase
{
    use CreatesApplication;

    public function testExport()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);

        $header = ['id', 'title', 'content'];
        $exportCsv = new ExportService($header, 'topic' . date('YmdHis'));

        // 减少IO次数，一般设置为 5000 ~ 10000
        $limit = 10000;
        $lastId = 0;

        while (
            // 此处可以使用原生sql 来提高查询效率
            // 默认 order by id asc, 可以根据业务调整 $lastId
            $data = Topic::query()
                ->select('id', 'title', 'content')
                ->where('id', '>', $lastId)
                ->limit($limit)
                ->get()
                ->toArray()
        ) {
           foreach ($data as $item) {
               $csv = [];
               $csv[] = $item['id'];
               $csv[] = $item['title'];
               $csv[] = $item['content'];
               $exportCsv->outputRow($csv);
           }
            $lastId = $data[count($data) - 1]['id'];
        }
        $this->assertTrue(true);
    }
}
