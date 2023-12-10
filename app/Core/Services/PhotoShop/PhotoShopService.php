<?php

namespace App\Core\Services\PhotoShop;

use App\Configs\PhotoShopConfigs;
use Intervention\Image\ImageManagerStatic;

class PhotoShopService
{
    public static function processImageWithBaseTemplate(string $source): \Intervention\Image\Image
    {
        $main = ImageManagerStatic::make(PhotoShopConfigs::TEMPLATE_PATH)->widen(1280);
        $img = ImageManagerStatic::make($source)->heighten(PhotoShopConfigs::DEFAULT_HEIGHT_AMAZON_PRODUCT_IMAGE);
        $main->insert($img, PhotoShopConfigs::DEFAULT_POSITION_AMAZON_PRODUCT_IMAGE);
        return $main;
    }
}