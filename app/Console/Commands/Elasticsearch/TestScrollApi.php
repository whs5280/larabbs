<?php

namespace App\Console\Commands\Elasticsearch;

use App\Http\ESBuilders\TopicsBuilder;
use Illuminate\Console\Command;

class TestScrollApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:scroll-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scroll Api usage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle()
    {
        $builder = (new TopicsBuilder)->initScroll(3);
        $result = app('es')->search($builder->getParams());
        print_r(self::getId($result));

        $scrollId = $result['_scroll_id'];
        do {
            $response = app('es')->scroll($builder->continueScroll($scrollId));
            print_r(self::getId($response));

            $scrollId = $response['_scroll_id'];
        } while (count($response['hits']['hits']) > 0);
    }

    /**
     * @param $result
     * @return array
     */
    public static function getId($result): array
    {
        return array_column($result['hits']['hits'], '_id');
    }
}
