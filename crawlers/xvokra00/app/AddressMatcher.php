<?php

namespace App;

class AddressMatcher
{
    const BTC = 0;
    const BCH = 1;
    const LTC = 2;
    const DASH = 3;
    const ZEC = 4;
    const ETH = 5;
    const COINS = [self::BTC, self::BCH, self::LTC, self::DASH, self::ZEC, self::ETH];
    const REGEXES = [
        self::BTC => '([13][a-km-zA-HJ-NP-Z1-9]{25,33}|bc1([A-Za-z0-9]{39}|[A-Za-z0-9]{59}))',
        self::BCH => '[13][a-km-zA-HJ-NP-Z1-9]{25,33}',
        self::LTC => '[LM3][a-km-zA-HJ-NP-Z1-9]{25,33}',
        self::DASH => 'X[1-9A-HJ-NP-Za-km-z]{25,33}',
        self::ZEC => 't[a-zA-Z0-9]{34}',
        self::ETH => '0x[a-fA-F0-9]{40}',
    ];

    public static function identifyAddress($address)
    {
        $result = [];

        foreach (self::REGEXES as $coin => $regex) {
            if (preg_match('/^' . $regex . '$/', $address)) {
                $result[] = $coin;
            }
        }

        return $result;
    }

    public static function matchAddresses($text)
    {
        $results = [];
        $regexes = array_values(self::REGEXES);

        foreach ($regexes as $regex) {
            $res = preg_match_all(self::wrapRegex($regex), $text, $matches);
            $addresses = array_unique($matches[1]); # 0 => whole regex, 1 => inside parens

            foreach ($addresses as $address) {
                $results[$address] = self::identifyAddress($address);
            }
        }

        return $results;
    }

    public static function wrapRegex($regex)
    {
        return '/[>\s](' . $regex . ')[<\s]/';
    }
}
