<?php

namespace App\ThirdParty\Test;

use App\ThirdParty\Service\TrieTree;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class TrieTreeTest extends BaseTestCase
{
    use CreatesApplication;

    public function testTreeQuery()
    {
        $strList = ['春风十里', '春天在哪里', '一百万个可能', '一千年以后', '后来', '后来的我们', '春天里', '后会无期'];
        $queryRes = TrieTree::init($strList)->query('春天');
        $this->assertEquals(['春天在哪里', '春天里'], $queryRes);
    }
}
