<?php

namespace App\Core\Services\Telegram;

use App\Configs\GeneralConfigurations;
use App\Core\Enums\LanguageCode;
use App\Core\ORM\Repositories\ScoreRepository;
use App\Core\Services\CacheService;
use App\Integrations\Telegram\TelegramClient;
use function DI\get;

class RankingService
{
    private ScoreRepository $scoreRepository;

    private CacheService $cacheService;

    public function __construct(ScoreRepository $scoreRepository, CacheService $cacheService)
    {
        $this->scoreRepository = $scoreRepository;
        $this->cacheService = $cacheService;
    }

    public function sendTopOfWeek(): void
    {
        $adding_text = $this->cacheService->getKey(GeneralConfigurations::RANKING_ADDING_TEXT_CACHE_KEY);
        $users = $this->scoreRepository->getTopOfWeek();
        $text = "";
        $i = 1;
        foreach ($users as $user) {
            $text .= "[ $i ] : " . TelegramClient::getChat($user['user_id'])['result']['first_name'] . " (" . $user['total_score'] . ")\n";
            $i++;
        }
        $text = get_string(LanguageCode::IT, "ranking", $text, $adding_text);
        TelegramClient::sendMessage(
            chat_id: GeneralConfigurations::GROUP_CHAT_ID,
            text: $text,
            parse_mode: 'HTML'
        );
    }
}