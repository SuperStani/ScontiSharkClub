<?php


use App\Core\Controllers\PhotoShop\PhotoShopController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

header('Access-Control-Allow-Origin: *');

require_once __DIR__ . "/../../../../vendor/autoload.php";


try {
    $container = require_once __DIR__ . "/../../../Configs/DIConfigs.php";
    $app = $container->get(PhotoShopController::class);
    $app->init();
    $app->process();
} catch (NotFoundExceptionInterface|ContainerExceptionInterface|Exception $e) {
    echo $e->getMessage();
}