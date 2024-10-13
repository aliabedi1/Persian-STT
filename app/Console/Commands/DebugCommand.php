<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DebugCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug';

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
        dd(getTextFromSpeechAvalAi('c814a66d5b3e4c0393f8952a0425ba0deeb905b0.mp3'));
    }
}
