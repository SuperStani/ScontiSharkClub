<?php


namespace App\Configs;


use App\Core\Enums\LanguageCode;

interface GeneralConfigurations
{
    public const LOGGER_PATH = "/var/log/ScontiSharkClub/";
    public const BOT_TOKEN = "6798069040:AAH7yd42idhZFy2e9bg6iB_mHYVGbD8mxDo";
    public const GROUP_CHAT_ID = -1002094340294;
    public const GROUP_CHAT_URL = 'https://t.me/ScontiSharkClub';
    public const ADMINS = [
        170172016,
        406343901
    ];
    public const DEFAULT_LANG = LanguageCode::IT;

    public const TOTAL_PRODUCTS_ALLOWED_PER_DAY = 10;
    public const WEBAPP_ACTIVE = false;
    public const RANKING_ADDING_TEXT_CACHE_KEY = 'RANKING_ADD';
    public const WELCOME_MESSAGE_CACHE_KEY = 'WELCOME_MESSAGE';

    public const KEEPA_API_ACCESS_KEY = "8a6ednbmffm7mm9d9snllhftd5gmffpj6li3qn5sr27gck0c0d8cjjbuct6g1vgh";

    public const AMAZON_REF = "scontisharkclub-21";
}