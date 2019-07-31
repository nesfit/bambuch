<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

use App\AddressMatcher;
use App\Models\Pg\Category;
use App\Models\Pg\Address;
use App\Models\Pg\Identity;
use App\Models\Pg\Owner;


class BitcoinabuseImport extends Bitcoinabuse
{
    protected $signature = 'bitcoinabuse:import {file}';
    protected $description = 'Bitcoinabuse.com import';


    public function handle()
    {
        $fd = fopen($this->argument('file'), 'r');
        while (($data = fgetcsv($fd, 0, ';')) !== FALSE ) {
            list($address, $abuser, $category, $url, $description) = $data;
            $report = [
                'address' => $address,
                'abuser' => $abuser,
                'url' => $url,
                'category' => $category,
                'description' => $description,
            ];
            $this->saveReport($report);
        }
        fclose($fd);
    }
}
