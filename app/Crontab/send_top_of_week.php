<?php

use App\Core\Services\Telegram\RankingService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

header('Access-Control-Allow-Origin: *');

require_once __DIR__ . "/../../vendor/autoload.php";
require __DIR__ . "/../Langs/getlang.php";


try {
    $container = require_once __DIR__ . "/../Configs/DIConfigs.php";
    $app = $container->get(RankingService::class);
    $app->sendTopOfWeek();
} catch (NotFoundExceptionInterface|ContainerExceptionInterface|Exception $e) {
    echo $e->getMessage();
}