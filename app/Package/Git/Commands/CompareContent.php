<?php

namespace App\Package\Git\Commands;

use App\Package\Git\Services\FileDiffCalculator;
use App\Package\Git\Services\VersionControl;
use Illuminate\Console\Command;

class CompareContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'git:diff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'compare content of two commits';

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
     * @throws \Throwable
     */
    public function handle()
    {
        $option = $this->choice('What do you want to do?', ['1' => 'commit', '2' => 'file']);
        switch ($option) {
            case 'commit':
                $this->gitCommitAndShowDiff();
                break;
            case 'file';
                $this->showDiffByFile();
                break;
        }
    }

    /**
     * @throws \Throwable
     */
    public function gitCommitAndShowDiff()
    {
        $dataJsonV1 = '{"path": "app/Package/Git/GitServiceProvider", "content": "Hello, World V1", "author": "wanghs", "message": "FEAT: add content"}';
        $dataJsonV2 = '{"path": "app/Package/Git/GitServiceProvider", "content": "Hello, World V2", "author": "wanghs", "message": "FIX: fix content"}';
        $dataV1 = json_decode($dataJsonV1, true);
        $dataV2 = json_decode($dataJsonV2, true);

        $vc = new VersionControl();
        $commitHashV1 = $vc->commitFile($dataV1['path'], $dataV1['content'], $dataV1['author'], $dataV1['message']);
        $commitHashV2 = $vc->commitFile($dataV2['path'], $dataV2['content'], $dataV2['author'], $dataV2['message']);

        $this->info("commit hash v1: $commitHashV1");
        $this->info("commit hash v2: $commitHashV2");

        $diffOutput = $vc->getDiff($commitHashV1, $commitHashV2);
        $this->info($diffOutput);
    }

    public function showDiffByFile()
    {
        $contentV1 = file_get_contents('app/Package/Git/Models/GitFile.php');
        $contentV2 = file_get_contents('app/Package/Git/Models/GitBlob.php');

        $diff = FileDiffCalculator::calculateLineDiff($contentV1, $contentV2);
        dd($diff);
    }
}
