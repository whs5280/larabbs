<?php

namespace App\Console\Commands\Novel;

use App\ThirdParty\Service\NovelService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;

class ChapterReader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chapter:reader';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Novel Chapter Reader';

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
     * @throws GuzzleException
     */
    public function handle()
    {
        $option = $this->choice('Choose an option to download', ['1' => 'download catalogue', '2' => 'download chapter']);
        switch ($option) {
            case 'download catalogue':
                NovelService::acquireCatalogueTextOnline();
                break;
            case 'download chapter':
                NovelService::acquireChapterTextOnline("http://www.xliangyusheng.com/33/33153/16078411.html");
                break;
        }
    }
}
