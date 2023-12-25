<?php

namespace App\Lottery\Commands;

use App\Lottery\Models\Prize;
use App\Lottery\Support\CacheKey;
use App\Lottery\Support\RedisList;
use Illuminate\Console\Command;

class HandlePrizeCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handle-prize-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步奖品数量到Redis';

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Prize::all()->map(function ($prize) {
            $prize->stock > 0
            && (new RedisList(
                sprintf("%s%s", CacheKey::PRIZE_STOCK, $prize->id)
            ))->pipelinePush(
                array_fill(0, $prize->stock, $prize->id)
            );
        });

        $this->info('sync prize count to redis success');
    }
}
