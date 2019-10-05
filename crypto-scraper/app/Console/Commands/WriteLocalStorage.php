<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Storage;

class WriteLocalStorage extends CryptoParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:write {data} {dateTime?} {verbose=1}';

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
        $this->verbose = $this->argument("verbose");
        $data = $this->argument("data");
        $dateTime = $this->argument("dateTime") ?? date("Y-m-d H:i:s");
        $fileName = "scrapper_data_" . date("Y_m_d_H_i", strtotime($dateTime));
//        $this->printHeader("<fg=yellow>Writing into file: </>" . $fileName);
        Storage::disk("local")->append($fileName, $data);
        return 1;
    }
}
