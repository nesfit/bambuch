<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BitcointalkLoadAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:load_all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads all bitcointalk boards';

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
