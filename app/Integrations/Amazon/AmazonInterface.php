<?php

namespace App\Integrations\Amazon;

interface AmazonInterface
{
    public function getProductInfo(string $url): ?AmazonProduct;

    public function validateUrl(string $url): bool;

}