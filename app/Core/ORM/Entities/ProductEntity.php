<?php

namespace App\Core\ORM\Entities;

use App\Core\ORM\Entities\AbstractEntity;

class ProductEntity extends AbstractEntity
{
    private ?int $id;
    private string $url;
    private int $sharedByUserId;

    private string $asin;

    public function __construct(string $url, int $sharedByUserId, string $asin, ?int $id = null)
    {
        $this->id = $id;
        $this->sharedByUserId = $sharedByUserId;
        $this->url = $url;
        $this->asin = $asin;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getSharedByUserId(): int
    {
        return $this->sharedByUserId;
    }

    public function setSharedByUserId(int $sharedByUserId): void
    {
        $this->sharedByUserId = $sharedByUserId;
    }

    public function getAsin(): string
    {
        return $this->asin;
    }

    public function setAsin(string $asin): void
    {
        $this->asin = $asin;
    }

}