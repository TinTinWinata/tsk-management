<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Schedule;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Configuration;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Str;
use LINE\Clients\MessagingApi\Model\PushMessageRequest;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;

class LineController extends Controller
{
    private static $api = null;
    protected $commands;
    protected $prefix;

    public function __construct()
    {
        $this->prefix = '/';
        $this->commands = [
            'login' => 'Try to login with \'/login [Email]:[Password]\'',
            'schedules' => 'See your task for today!',
            'help' => 'We\'re always here to help you!',
            'notes' => 'Get all notes from your our web app!',
            'addnote' => 'Add a note to your notebook \'/addNote [Note Title]:[Note Content]\'',
            'note' => 'Get your note in our web app \'/note [Note Number]\'',
            'myid' => 'Get your Line ID'
        ];
    }

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

    public function login($text, $user, $replyToken, $lineId)
    {
        $data = explode(":", $text);

        $email = $data[0];
        $password = $data[1];
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = User::where('email', $email)->first();

            $user->line_id = $lineId;
            $user->save();

            $this->replyMessage("Succesfully integrated to Tsk Management 😊!\n\nWelcome, " . $user->name . "\n\n Try to '/help' to see more information.", $replyToken);
            return response()->json('Succesfully response', 200);
        };
        return response()->json('User not found', 400);
    }


    public function note($text, $user, $replyToken)
    {
        $replyText = "";
        try {
            $idx = intval($text) - 1;

            if ($user->notes[$idx]) {
                $note = $user->notes[$idx];
                $replyText = "This is your " . $note->title . " Note:\n\n" . $note->content;
            }
        } catch (Exception $e) {
            $replyText = "Oh no, look's like your note cannot be found! 😢";
        }
        return $this->handleSuccessResponse($replyText, $replyToken);
    }

    public function myid($text, $user, $replyToken, $lineId)
    {
        return $this->handleSuccessResponse('This is your line id 😊: ' . $lineId, $replyToken);
    }

    public function addnote($text, $user, $replyToken)
    {
        $data = explode(":", $text);

        $title = $data[0];
        $content = $data[1];

        $replyText = "";
        $note = Note::create([
            'title' => $title,
            'content' => $content,
            'user_id' => $user->id
        ]);
        if ($note) {
            $replyText = "Succesfully create " . $title . " notes! Thankyou! 😊";
        } else {
            $replyText = "Oh no, look's like the note can't be created! 😢";
        }
        return $this->handleSuccessResponse($replyText, $replyToken);
    }

    public function notes($text, $user, $replyToken)
    {
        $replyText = "";
        if (count($user->notes) <= 0) {
            $replyText = "Ups 😯, look's like you don't have any notes.";
        } else {
            $replyText = "Hello " . $user->name . " 😊\nThis is your available notes:\n\n";
            foreach ($user->notes as $key => $note) {
                $replyText .= $key + 1 . ") " . $note->title . "\n";
            }
            $replyText .= "\nYou can get the details for the note by : '/note [Note Id]'";
        }
        return $this->handleSuccessResponse($replyText, $replyToken);
    }

    public function help($text, $lineId, $replyToken)
    {
        $replyText = "Welcome to tsk management Web App 😊, to try our features you can:\n\n";
        foreach ($this->commands as $command => $text) {
            $replyText .= "- " . $this->prefix . $command . " " . $text . "\n";
        }
        $replyText .= "\nAnd you can visit our app on " . env('APP_URL');
        return $this->handleSuccessResponse($replyText, $replyToken);
    }

    public function handleSuccessResponse($replyText, $replyToken)
    {
        $this->replyMessage($replyText, $replyToken);
        return response()->json('Succesfully response', 200);
    }

    public function schedules($text, $user, $replyToken)
    {
        $replyText = "";
        if (count($user->schedulesToday) <= 0) {
            $replyText = "You don't have any schedules today.";
        } else {
            $replyText = "Hello " . $user->name . " 😊\nThis is your schedules for today:\n\n";
            foreach ($user->schedulesToday as $key => $schedule) {
                $replyText .= $key + 1 . ") " . $schedule->title . "\n";
            }
        }
        return $this->handleSuccessResponse($replyText, $replyToken);
    }

    public function mediator($text, $lineId, $replyToken)
    {
        $user = User::where('line_id', $lineId)->first();
        if (!$user) {
            return $this->handleAuthenticated($replyToken);
        }
        foreach ($this->commands as $command => $commandText) {
            if (Str::startsWith($text, $this->prefix . $command)) {
                $parts = explode(' ', $text);
                array_shift($parts);
                $extractedText = trim(implode(' ', $parts));
                return $this->{$command}($extractedText, $user, $replyToken, $lineId);
            }
        }
        return $this->handleUnknownCommand($replyToken);
    }

    public function handleAuthenticated($replyToken)
    {
        $this->replyMessage("I don't know who you are yet 😯, try to '/login'", $replyToken);
        return response()->json('Authenticated', 403);
    }

    public function handleUnknownCommand($replyToken)
    {
        $this->replyMessage("If you're loss, try to '/help' 😊", $replyToken);
        return response()->json('Unknown Command', 404);
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
            try {
                $this->mediator($text, $userId, $replyToken);
            } catch (Exception $e) {
                Log::error($e);
                return $this->handleSuccessResponse("Oh no 😢, look's like you made a terrible thing!", $replyToken);
            }
        } else {
            return response()->json('Error validating data', 404);
        }
    }
}
