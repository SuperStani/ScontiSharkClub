<?php

namespace App\Integrations\Amazon\AmazonPaapi;

use App\Integrations\Amazon\AmazonInterface;
use App\Integrations\Amazon\AmazonProduct;

class AmazonPaapi implements AmazonInterface
{
    private static string $commandPath = "python3 /scripts/ScontiSharkClub/app/Integrations/AmazonPaapi/amazonScraper.py";

    public function getProductInfo(string $url): ?AmazonProduct
    {
        $res = shell_exec(self::$commandPath . " \"$url\"");
        if ($res == '')
            return null;
        $data = json_decode($res, true);
        return new AmazonProduct(
            $data['title'],
            $data['url'],
            $data['actualPrice'],
            $data['lowerPrice'],
            $data['highestPrice'],
            $data['photo']
        );
    }

    public function validateUrl(string $url): bool
    {
        return strstr($url, "amazon") || strstr($url, "amzn");
    }

    public function extractAsin(string $url)
    {

    }

}