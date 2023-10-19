<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Configuration;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Str;
use LINE\Clients\MessagingApi\Model\PushMessageRequest;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;

class LineController extends Controller
{
    private static $api = null;

    public function test(Request $req)
    {
        $line_id = $req->user()->line_id;
        if ($line_id) {
            LineController::pushMessage("Hello! This is a test message!", $line_id);
            return response()->json('Line message sended!', 200);
        } else {
            return response()->json('You\'re Line is not connected.', 400);
        }
    }

    public static function getMessagingApi()
    {
        if (LineController::$api == null) {
            $client = new Client();
            $config = new Configuration();

            $accessToken = env('LINE_BOT_ACCESS_TOKEN');
            $config->setAccessToken($accessToken);

            LineController::$api = new MessagingApiApi(
                client: $client,
                config: $config,
            );
        }

        return LineController::$api;
    }

    public static function pushMessage($text, $lineId)
    {
        $api = LineController::getMessagingApi();
        $message = new TextMessage(['type' => 'text', 'text' => $text]);
        $push = new PushMessageRequest([
            'to' => $lineId,
            'messages' => [$message]
        ]);
        $api->pushMessage($push);
    }

    public function replyMessage($text, $replyToken)
    {
        $api = LineController::getMessagingApi();
        $message = new TextMessage(['type' => 'text', 'text' => $text]);
        $request = new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages' => [$message]
        ]);
        $api->replyMessage($request);
    }

    public function login($text, $lineId, $replyToken)
    {
        $data = explode(" ", $text);

        $email = $data[1];
        $password = $data[2];
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = User::where('email', $email)->first();

            $user->line_id = $lineId;
            $user->save();

            $this->replyMessage("Succesfully integrated to Task Management!\nWelcome, " . $user->name, $replyToken);
            return response()->json('Succesfully response', 200);
        };
        return response()->json('User not found', 400);
    }

    public function webhook(Request $req)
    {
        $req->validate([
            'events' => 'required',
            'destination' => 'required'
        ]);
        $event = $req->events[0];

        $text = $event['message']['text'];
        $replyToken = $event['replyToken'];
        $userId = $event['source']['userId'];

        if ($text && $replyToken && $userId) {
            if (Str::startsWith($text, '/login')) return $this->login($text, $userId, $replyToken);
        } else {
            return response()->json('Error validating data', 404);
        }
    }
}
