<?php

namespace App\Core\Controllers\Telegram\Messages;

use App\Configs\GeneralConfigurations;
use App\Core\Controllers\Telegram\MessageController;
use App\Core\Controllers\Telegram\UserController;
use App\Core\Enums\LanguageCode;
use App\Core\Logger\LoggerInterface;
use App\Core\ORM\Repositories\ScoreRepository;
use App\Core\Services\CacheService;
use App\Integrations\Telegram\Enums\Message;


class CommandsController extends MessageController
{
    private CacheService $cacheService;

    private ScoreRepository $scoreRepository;

    public function __construct(
        Message         $message,
        UserController  $user,
        LoggerInterface $logger,
        CacheService    $cacheService,
        ScoreRepository $scoreRepository
    )
    {
        parent::__construct($message, $user, $logger);
        $this->cacheService = $cacheService;
        $this->scoreRepository = $scoreRepository;
    }

    public function start($param = null): void
    {
        if (!$param) {
            $text = get_string(
                LanguageCode::IT,
                'start',
                $this->user->getName(),
                $this->scoreRepository->getScoreOfWeekByUserId($this->user->getId())
            );
            $this->message->reply(text: $text, parse: 'HTML');
        } else {
            $param = explode("_", $param);
            switch ($param[0]) {
                default:
                    break;
            }
        }
    }

    public function setwelcome(string $message)
    {

    }

    public function setrank(string $message = ''): void
    {
        $this->cacheService->setKey(GeneralConfigurations::RANKING_ADDING_TEXT_CACHE_KEY, $message);
        $this->message->reply($message);
        $this->message->delete();
    }

}
