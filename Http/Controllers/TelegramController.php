<?php

namespace Modules\Telegram\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Telegram\Services\TelegramService;

class TelegramController extends Controller
{
    public function getMessages()
    {
        $telegramService = new TelegramService(config('telegram.bot'));
        return $telegramService->getAllMessages();

        // TODO: return view('telegram::index');
    }

    public function sendMessage(Request $request): JsonResponse
    {
        Log::channel('api')->info('Отправка сообщения в Телеграм', [
            'data' => $request->only(['chat_id', 'message']),
            'ip' => $request->ip(),
        ]);

        try {
            $errors = [];
            $result = null;
            $data = null;

            $data['chat_id'] = $request->get('chat_id');

            if (empty($data['chat_id'])) {
                $errors[] = 'Не задан идентификатор чата';
            }

            $data['message'] = $request->get('message');

            if (empty($data['message'])) {
                $errors[] = 'Не задано сообщение';
            }

            if (empty($errors)) {
                $telegramService = new TelegramService(config('telegram.bot'));
                $responseData = $telegramService->sendMessage($data['chat_id'], $data['message'])->json();

                if ($responseData['ok']) {
                    $result = $responseData['result'];
                } else {
                    $errors[] = $responseData['description'];
                }
            }
        } catch (Exception $e) {
            $errors[] = "Oops, something went wrong";

            Log::channel('api')->error(
                sprintf(
                    'An error occurred while trying to send a message to Telegram, error code: %s',
                    $e->getCode()
                ),
                [
                    'Exception class' => get_class($e),
                    'Message' => $e->getMessage(),
                    'File' => $e->getFile(),
                    'Line' => $e->getLine(),
                    'Trace' => $e->getTrace(),
                ]
            );
        }

        return response()->json([
            'success' => empty($errors),
            'errors' => $errors,
            'result' => $result,
        ]);
    }
}
