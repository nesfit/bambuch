<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BitcointalkLoad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:load_board';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load bitcointalk board.';

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
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
