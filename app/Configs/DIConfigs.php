<?php

use App\Configs\DatabaseCredentials;
use App\Configs\GeneralConfigurations;
use App\Configs\RedisConfigurations;
use App\Core\Controllers\Cache\RedisController;
use App\Core\Logger\Logger;
use App\Core\Logger\LoggerInterface;
use App\Core\ORM\DB;
use App\Core\Services\Telegram\UpdateService;
use App\Integrations\Amazon\AmazonInterface;
use App\Integrations\Amazon\Keepa\Keepa;
use App\Integrations\Bitly\BitlyAPI;
use App\Integrations\Telegram\Enums\Update;
use DI\ContainerBuilder;
use GuzzleHttp\Client;
use Keepa\KeepaAPI;
use Psr\Container\ContainerInterface;
use function DI\factory;


$conf = [
    LoggerInterface::class => factory(function (ContainerInterface $c) {
        return new Logger();
    }),
    DB::class => factory(function (ContainerInterface $c) {
        return new DB(
            $c->get(LoggerInterface::class),
            DatabaseCredentials::HOST,
            DatabaseCredentials::PORT,
            DatabaseCredentials::USER,
            DatabaseCredentials::PASSWORD,
            DatabaseCredentials::DATABASE
        );
    }),
    RedisController::class => factory(function () {
        return new RedisController(
            RedisConfigurations::HOST,
            RedisConfigurations::PORT,
            RedisConfigurations::SOCKET
        );
    }),
    Update::class => factory(function () {
        return UpdateService::get();
    }),
    KeepaAPI::class => factory(function () {
        return new KeepaAPI(GeneralConfigurations::KEEPA_API_ACCESS_KEY);
    }),
    AmazonInterface::class => factory(function (ContainerInterface $c) {
        //return $c->get(AmazonPaapi::class);
        return $c->get(Keepa::class);
    }),
    BitlyAPI::class => factory(function (ContainerInterface $c) {
        return new BitlyAPI(
            token: GeneralConfigurations::BITLY_TOKEN,
            guid: GeneralConfigurations::BITLY_GUID,
            http: $c->get(Client::class),
            logger: $c->get(LoggerInterface::class)
        );
    })
];

$builder = new ContainerBuilder();
$builder->addDefinitions($conf);
return $builder->build();
