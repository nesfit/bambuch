<?php
declare(strict_types=1);

namespace App\Console\Constants\Bitcoinabuse;

abstract class BitcoinabuseCommands {
    const BITCOINABUSE = 'bca:';
    const BITCOINABUSE_URL = 'https://www.bitcoinabuse.com';

    /**
     * COMMAND SIGNATURES
     */
    const LOAD_CSV_DATA = self::BITCOINABUSE . 'load_csv_data';
    
    /**
     * COMMAND DESCRIPTIONS
     */
    const LOAD_CSV_DATA_DESC = 'Loads records in CSV format from bitcoinabuse.com';
}