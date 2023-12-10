<?php

namespace App\Core\Services\Amazon;

use App\Integrations\AmazonPaapi\Enums\AmazonProduct;
use App\Integrations\Telegram\TelegramClient;

class AmazonService
{
    private static string $commandPath = "python3 /scripts/ScontiSharkClub/app/Integrations/AmazonPaapi/amazonScraper.py";

    public static function getProductInfo(string $url): ?AmazonProduct
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

    public static function validateUrl(string $url): bool
    {
        return strstr($url, "amazon") || strstr($url, "amzn");
    }
}