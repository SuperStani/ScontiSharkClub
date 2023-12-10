<?php

namespace App\Core\Controllers\Telegram\Query;

use App\Core\Controllers\Telegram\QueryController;
use App\Core\Controllers\Telegram\UserController;
use App\Core\Enums\LanguageCode;
use App\Core\Logger\LoggerInterface;
use App\Core\ORM\Repositories\ScoreRepository;
use App\Integrations\Telegram\Enums\Query;

class ScoreController extends QueryController
{
    private ScoreRepository $scoreRepository;

    public function __construct(
        Query           $query,
        UserController  $user,
        LoggerInterface $logger,
        ScoreRepository $scoreRepository
    )
    {
        parent::__construct($query, $user, $logger);
        $this->scoreRepository = $scoreRepository;
    }

    public function vote(int $score, int $productId): ?array
    {
        $this->scoreRepository->voteByProductId($score, $productId, $this->user->getId());
        return $this->query->alert(get_string(LanguageCode::IT, "vote", "$score ⭐️"));
    }
}