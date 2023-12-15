<?php

namespace App\Integrations\Amazon;

interface AmazonInterface
{
    public function getProductInfo(string $asin): ?AmazonProduct;

    public function validateUrl(string $url): bool;

    public function getASINFromURL(string $url): string;

}