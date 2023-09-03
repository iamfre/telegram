<?php

namespace Modules\Telegram\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    const URL = 'https://api.telegram.org/bot';

    protected string $bot;
    protected Http $http;

    public function __construct($bot)
    {
        $this->http = new Http();
        $this->bot = $bot ?? config('telegram.bot');
    }

    public function getAllMessages()
    {
        $action = 'getUpdates';
        $url = self::URL.$this->bot."/".$action;

        $response = $this->http::withoutVerifying()
            ->withOptions(["verify" => false])
            ->post($url);

        return $response->json('result');
    }

    public function sendMessage(int $chatId, string $message): PromiseInterface|Response
    {
        $action = 'sendMessage';

        $url = self::URL.$this->bot."/".$action;

        $data = [
            'chat_id' => $chatId,
            'text' => view('telegram::messages.greeting', ['message' => $message])->render(),
            'parse_mode' => 'html',
        ];

        return $this->http::withOptions(['verify' => false])->post($url, $data);
    }
}
