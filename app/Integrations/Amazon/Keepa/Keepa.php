<?php

namespace App\Integrations\Amazon\Keepa;

use App\Configs\GeneralConfigurations;
use App\Core\Logger\LoggerInterface;
use App\Integrations\Amazon\AmazonInterface;
use App\Integrations\Amazon\AmazonProduct;
use App\Integrations\Telegram\TelegramClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\TransferStats;
use Keepa\API\Request;
use Keepa\helper\CSVType;
use Keepa\helper\CSVTypeWrapper;
use Keepa\helper\ProductAnalyzer;
use Keepa\KeepaAPI;
use Keepa\objects\AmazonLocale;

class Keepa implements AmazonInterface
{
    private KeepaAPI $api;

    private Client $http;

    private LoggerInterface $logger;

    public function __construct(KeepaAPI $api, Client $http, LoggerInterface $logger)
    {
        $this->api = $api;;
        $this->http = $http;
        $this->logger = $logger;
    }

    public function getProductInfo(string $url): ?AmazonProduct
    {
        $asin = $this->getASINFromURL($url);
        $r = Request::getProductRequest(
            domainID: AmazonLocale::IT,
            offers: 20,
            statsStartDate: null,
            statsEndDate: null,
            update: 1,
            history: true,
            asins: [$asin]
        );
        try {
            $response = $this->api->sendRequest($r);
            $product = $response->products[0];
            $currentAmazonPrice = ProductAnalyzer::getLast($product->csv[CSVType::AMAZON], CSVTypeWrapper::getCSVTypeFromIndex(CSVType::AMAZON));
            if ($currentAmazonPrice === -1) {
                TelegramClient::debug('Not available');
                return null;
            }
            $p = ProductAnalyzer::getLowestAndHighest($product->csv[CSVType::AMAZON], CSVTypeWrapper::getCSVTypeFromIndex(CSVType::AMAZON));

            $coupon = $product->coupon[0] ?? 0;
            if($coupon !== 0) {
                if ($coupon < 0) {
                    $coupon *= -1;
                    $coupon = $coupon / 100 * $currentAmazonPrice;
                } else{
                    $coupon /= 100;
                }
            }

            return new AmazonProduct(
                $product->title,
                "https://www.amazon.it/dp/{$product->asin}/?tag=" . GeneralConfigurations::AMAZON_REF,
                $currentAmazonPrice / 100 - $coupon,
                $p[0] / 100,
                $p[1] / 100,
                "https://images-na.ssl-images-amazon.com/images/I/" . explode(",", $product->imagesCSV)[0]
            );

            //TelegramClient::debug($response);

        } catch (\Exception $e) {
            $this->logger->error('getProductInfo', $e->getMessage());
            return null;
        }
    }

    public function getASINFromURL(string $url): string
    {
        try {
            $res = $this->http->get($url, [
                'on_stats' => function (TransferStats $stats) use (&$url) {
                    $url = $stats->getEffectiveUri();
                }]);
        } catch (\Exception|GuzzleException $e) {
            $this->logger->error("getASINFromURL", $e->getMessage());
            return '';
        }

        $asin_pattern = '/\/([A-Z0-9]{10})(?:[\/?]|$)/';
        preg_match($asin_pattern, $url, $match);
        return $match[1] ?? '';
    }

    public function validateUrl(string $url): bool
    {
        return strstr($url, "amazon") || strstr($url, "amzn");
    }
}