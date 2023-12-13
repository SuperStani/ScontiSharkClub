<?php

namespace App\Integrations\AmazonPaapi\Enums;

class AmazonProduct
{
    private string $title;
    private string $url;
    private ?float $price;
    private ?float $lowerPrice;
    private ?float $highestPrice;
    private string $photo;


    public function __construct(string $title, string $url, ?float $price, ?float $lowerPrice, ?float $highestPrice, string $photo)
    {
        $this->title = $title;
        $this->url = $url;
        $this->price = $price;
        $this->lowerPrice = $lowerPrice;
        $this->highestPrice = $highestPrice;
        $this->photo = $photo;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
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
}