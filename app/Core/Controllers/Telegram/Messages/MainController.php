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
use App\Integrations\Telegram\Enums\Message;
use App\Integrations\Telegram\TelegramClient;

class MainController extends MessageController
{
    private ProductsRepository $productsRepository;

    public function __construct(
        Message            $message,
        UserController     $user,
        LoggerInterface    $logger,
        ProductsRepository $productsRepository
    )
    {
        parent::__construct($message, $user, $logger);
        $this->productsRepository = $productsRepository;
    }

    public function processChatMessage(): void
    {
        $this->message->delete();
        if (isset($this->message->text) && AmazonService::validateUrl($this->message->text) && $this->productsRepository->getTotalProductTodayByUserId($this->user->getId()) < GeneralConfigurations::TOTAL_PRODUCTS_ALLOWED_PER_DAY) {
            $product = AmazonService::getProductInfo($this->message->text);
            if ($product === null) {
                $this->logger->info("Not valid product", $this->message->text, $this->user->getId());
                return;
            }
            if ($product->getPrice() > $product->getLowerPrice()) {
                $minhist = "";
            } else {
                $minhist = get_string(LanguageCode::IT, 'minhist');
            }

            if ($product->getPrice() < $product->getHighestPrice()) {
                $price_template = get_string(
                    LanguageCode::IT,
                    'price_template_2',
                    $product->getPrice(),
                    $product->getHighestPrice(),
                    (int)(($product->getHighestPrice() - $product->getPrice()) / $product->getHighestPrice() * 100) . "%"
                );
            } else {
                $price_template = get_string(LanguageCode::IT, 'price_template_1', $product->getPrice());
            }

            $productEntity = new ProductEntity(url: $product->getUrl(), sharedByUserId: $this->user->getId());
            $this->productsRepository->saveProduct($productEntity);

            $caption = get_string(LanguageCode::IT, 'product_template', $product->getTitle(), $price_template, $minhist, $product->getUrl(), $product->getUrl());
            $menu[] = [
                ["text" => "1 ⭐️", "callback_data" => "Score:vote|1|" . $productEntity->getId()],
                ["text" => "2 ⭐️", "callback_data" => "Score:vote|2|" . $productEntity->getId()],
                ["text" => "3 ⭐️", "callback_data" => "Score:vote|3|" . $productEntity->getId()],
                ["text" => "4 ⭐️", "callback_data" => "Score:vote|4|" . $productEntity->getId()],
                ["text" => "5 ⭐️", "callback_data" => "Score:vote|5|" . $productEntity->getId()],
            ];
            $this->message->reply_photo(
                photo: PhotoShopConfigs::API_URL . "?sourceImage=" . $product->getPhoto(),
                caption: $caption,
                menu: $menu,
                parse: 'HTML'
            );
        }
    }
}