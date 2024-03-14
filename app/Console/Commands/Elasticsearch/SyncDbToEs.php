<?php

namespace App\Console\Commands\Elasticsearch;


use App\Models\Topic;
use App\Models\User;
use Illuminate\Console\Command;

class SyncDbToEs extends Command
{
    /**
     * @var string
     */
    protected $signature = 'es:sync-tables {index}';

    /**
     * @var string
     */
    protected $description = '将数据同步到 Elasticsearch';


    /**
     * Migrate constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected $models = [
        'topics_online' => Topic::class,
        'users_online'  => User::class,
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 获取 ES对象
        $es = app('es');

        // 获取参数
        $index = $this->argument('index');
        if (!$index) {
            $this->info('not index');die();
        }

        $modelClass = $this->models[$index];

        $modelClass::query()
            ->chunkById(100, function ($info) use ($es, $index) {
                $this->info(sprintf('正在同步 ID 范围为 %s 至 %s 的商品',
                    $info->first()->id, $info->last()->id
                ));

                // 初始化请求体
                $req = ['body' => []];
                // 遍历
                foreach ($info as $item) {
                    // 将商品模型转为 Elasticsearch 所用的数组
                    $data = $item->toESArray();

                    $req['body'][] = [
                        'index' => [
                            '_index' => $index,
                            '_type'  => '_doc',
                            '_id'    => $data['id'],
                        ],
                    ];
                    $req['body'][] = $data;
                }
                try {
                    // 使用 bulk 方法批量创建
                    $es->bulk($req);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            });

        $this->info($index. '同步完成');
    }
}
