<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class WriteLocalStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:write {data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Writes into local storage';

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
        $data = $this->argument("data");
        $fileName = "scrapper_data_" . date("Y_m_d_H_i");
        $this->info("Writing into file: " . $fileName);
        Storage::disk("local")->append($fileName, $data);
        return 1;
    }
}
