<?php

namespace App\Container\base;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class ContainerTest extends BaseTestCase
{
    use CreatesApplication;

    public function test()
    {
        $container = new Container();

        $configs = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'container.json'), true);
        logger()->info("-----container configs load-----", [$configs]);
        $this->assertTrue(is_array($configs));

        foreach ($configs as $class => $config) {
            try {
                $container->set($class, $config);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }

        try {
            // GoldPwdService
            $instance = $container->get('gold_pwd_service', []);
            logger()->info("-----container get instance-----", [get_class($instance)]);

            $encrypt = $instance::encrypt(['userId' => 10086, 'token' => '123456']);
            logger()->info("-----container encrypt-----", [$encrypt]);

            // TrieTree
            $instance02 = $container->get('trie_tree', []);
            logger()->info("-----container get instance-----", [get_class($instance02)]);
            $result = $instance02::init(['少年白马', '不负少年', '少年闲时'])->query('少年');
            logger()->info("-----container query-----", [$result]);

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        $this->assertTrue(true);
    }
}
