<?php

namespace App\Core\Controllers\Telegram\Messages;

use App\Configs\GeneralConfigurations;
use App\Configs\PhotoShopConfigs;
use App\Core\Controllers\Telegram\MessageController;
use App\Core\Controllers\Telegram\UserController;
use App\Core\Enums\LanguageCode;
use App\Core\Logger\LoggerInterface;
use App\Core\ORM\Entities\ProductEntity;
use App\Core\ORM\Repositories\ProductsRepository;
use App\Core\Services\Amazon\AmazonService;
use App\Core\Services\CacheService;
use App\Integrations\Telegram\Enums\Message;
use App\Integrations\Telegram\TelegramClient;

class MainController extends MessageController
{
    private ProductsRepository $productsRepository;
    private CacheService $cacheService;

    private AmazonService $amazonService;

    public function __construct(
        Message            $message,
        UserController     $user,
        LoggerInterface    $logger,
        ProductsRepository $productsRepository,
        CacheService       $cacheService,
        AmazonService      $amazonService
    )
    {
        parent::__construct($message, $user, $logger);
        $this->productsRepository = $productsRepository;
        $this->cacheService = $cacheService;
        $this->amazonService = $amazonService;
    }

    public function processChatMessage(): void
    {
        $this->message->delete();
        if (isset($this->message->text) && $this->amazonService->validateUrl($this->message->text) && $this->productsRepository->getTotalProductTodayByUserId($this->user->getId()) < GeneralConfigurations::TOTAL_PRODUCTS_ALLOWED_PER_DAY) {
            $this->processAmazonProduct();
        } elseif ($this->message->new_chat_members !== null) {
            $welcome_text = $this->cacheService->getKey(GeneralConfigurations::WELCOME_MESSAGE_CACHE_KEY);
            foreach ($this->message->new_chat_members as $user) {
                TelegramClient::debug(TelegramClient::sendMessage(chat_id: $user->id, text: $welcome_text, parse_mode: 'HTML'));
            }
        }
    }

    private function processAmazonProduct(): void
    {
        $product = $this->amazonService->getProductInfo($this->message->text);
        if ($product === null) {
            $this->logger->info("Not valid product", $this->message->text, $this->user->getId());
            return;
        }
        if ($product->getPrice() > $product->getLowerPrice()) {
            $minhist = "";
        } else {
            $minhist = "\n" . get_string(LanguageCode::IT, 'minhist');
        }

        if ($product->getPrice() < $product->getHighestPrice()) {
            $price_template = get_string(
                LanguageCode::IT,
                'price_template_2',
                str_replace(".", ",", $product->getPrice()),
                str_replace(".", ",", $product->getHighestPrice()),
                (int)(($product->getHighestPrice() - $product->getPrice()) / $product->getHighestPrice() * 100) . "%"
            );
        } else {
            $price_template = get_string(LanguageCode::IT, 'price_template_1', str_replace(".", ",", $product->getPrice()));
        }

        $productEntity = new ProductEntity(url: $product->getUrl(), sharedByUserId: $this->user->getId());
        $this->productsRepository->saveProduct($productEntity);

        $caption = get_string(LanguageCode::IT, 'product_template', $product->getTitle(), $price_template, $minhist, $product->getUrl(), $product->getUrl(), $this->user->getName());
        $menu[] = [
            ["text" => "1 ⭐️", "callback_data" => "Score:vote|1|" . $productEntity->getId()],
            ["text" => "2 ⭐️", "callback_data" => "Score:vote|2|" . $productEntity->getId()],
            ["text" => "3 ⭐️", "callback_data" => "Score:vote|3|" . $productEntity->getId()],
            ["text" => "4 ⭐️", "callback_data" => "Score:vote|4|" . $productEntity->getId()],
            ["text" => "5 ⭐️", "callback_data" => "Score:vote|5|" . $productEntity->getId()],
        ];
        $res = $this->message->reply_photo(
            photo: PhotoShopConfigs::API_URL . "?sourceImage=" . rawurlencode($product->getPhoto()) . "&time=" . time(),
            caption: $caption,
            menu: $menu,
            parse: 'HTML'
        );

        foreach (GeneralConfigurations::ADMINS as $admin) {
            TelegramClient::sendMessage(
                chat_id: $admin,
                text: get_string(LanguageCode::IT, 'product_admin_notification', GeneralConfigurations::GROUP_CHAT_URL . "/" . $res['result']['message_id'], $this->user->getId(), $this->user->getName()),
                parse_mode: 'HTML'
            );
        }
    }
}