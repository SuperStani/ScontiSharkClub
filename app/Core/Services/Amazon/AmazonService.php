<?php

namespace App\Core\Services\Amazon;

use App\Integrations\Amazon\AmazonInterface;
use App\Integrations\Amazon\AmazonProduct;

class AmazonService
{

    private AmazonInterface $amazon;

    public function __construct(AmazonInterface $amazon)
    {
        $this->amazon = $amazon;
    }

    public function getProductInfo(string $url): ?AmazonProduct
    {
        return $this->amazon->getProductInfo($url);
    }

    public function validateUrl(string $url): bool
    {
        return $this->amazon->validateUrl($url);
    }

    public function extractAsin(string $url)
    {

    }
}