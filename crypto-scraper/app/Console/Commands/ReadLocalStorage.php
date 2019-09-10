<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem;

class ReadLocalStorage extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:read {fileName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads local storage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return integer
     * @throws Filesystem\FileNotFoundException
     */
    public function handle() {
        $fileName = $this->argument("fileName");
        try {
            if (Storage::disk("local")->exists($fileName)) {
                $csvData = Storage::disk("local")->get($fileName);
                $csvRows =  explode("\n", $csvData);
                $progressBar = $this->output->createProgressBar(count($csvRows));
                foreach ($csvRows as $row) {
                    list($owner, $url, $label, $source, $address, $cryptoType) = explode(",", $row);
                    $this->call('insert:db', [
                        'owner name' => $owner,
                        'url' => $url,
                        'label' => $label,
                        'source' => $source,
                        'address' => $address,
                        'crypto type' => $cryptoType
                    ]);
                    $progressBar->advance();
                }
                $progressBar->finish();
                $this->line(""); // new line when progress bar finishes
            } else {
                $this->line("File not found: " . $fileName);
            }
        } catch (Filesystem\FileNotFoundException $exception) {
            $this->line("Read file exception");
            $this->error($exception);
            return 1;
        }
        return 0;
    }
}
