<?php

namespace App\Core\Controllers\PhotoShop;

use App\Core\Services\PhotoShop\PhotoShopService;

class PhotoShopController
{
    private ?string $sourceImage;

    public function __construct()
    {
        $this->sourceImage = null;
    }

    public function init(): void
    {
        if (isset($_GET['sourceImage'])) {
            $this->sourceImage = rawurldecode($_GET['sourceImage']);
        }
    }

    public function process(): void
    {
        if ($this->sourceImage !== null) {
            $image = PhotoShopService::processImageWithBaseTemplate($this->sourceImage);
            $this->response($image->response());
        } else {
            $this->response('Not valid request!', 'Content-type:application/json');
        }
    }

    private function response(mixed $response, $header = 'Content-type:image/png'): void
    {
        header($header);
        echo $response;
    }
}