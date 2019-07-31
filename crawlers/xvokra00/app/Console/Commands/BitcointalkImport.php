<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\AddressMatcher;


class BitcointalkImport extends Bitcointalk
{
    protected $signature = 'bitcointalk:import {resultsFile}';
    protected $description = 'Bitcointalk.com importer';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $filename = $this->argument('resultsFile');
        $fd = fopen($filename, 'r');

        while($line = fgets($fd)) {
            list($username, $address, $url, $label) = explode(',', trim($line), 4);
            $record = [
                'username' => $username,
                'label' => $label,
                'url' => $url,
                'category' => strpos('profile', $url) ? 2 : 1,
                'addresses' => [$address => AddressMatcher::identifyAddress($address)],
            ];
            $this->saveRecord($record);
        }

        fclose($fd);
    }
}
