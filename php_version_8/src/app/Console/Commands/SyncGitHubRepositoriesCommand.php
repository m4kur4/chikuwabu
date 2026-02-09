<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncGitHubRepositories extends Command
{
    protected $signature
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-git-hub-repositories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        print("hoge! piyo! figa!!\n");
    }
}
