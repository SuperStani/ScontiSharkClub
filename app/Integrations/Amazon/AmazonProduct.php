<?php

namespace App\Integrations\Amazon;

class AmazonProduct
{
    private string $title;
    private string $url;
    private ?float $price;
    private ?float $lowerPrice;
    private ?float $highestPrice;
    private string $photo;
    private string $asin;


    public function __construct(string $title, string $url, ?float $price, ?float $lowerPrice, ?float $highestPrice, string $photo, string $asin)
    {
        $this->title = $title;
        $this->url = $url;
        $this->price = $price;
        $this->lowerPrice = $lowerPrice;
        $this->highestPrice = $highestPrice;
        $this->photo = $photo;
        $this->asin = $asin;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }


    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getLowerPrice(): ?float
    {
        return $this->lowerPrice;
    }

    public function getHighestPrice(): ?float
    {
        return $this->highestPrice;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function getAsin(): string
    {
        return $this->asin;
    }


}