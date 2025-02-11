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
            'addNote' => 'Add a note to your notebook \'/addNote [Note Title]:[Note Content]\'',
            'addSchedule' => 'Add your schedule for today \'/addSchedule [Schedule Description]\'',
            'note' => 'Get your note in our web app \'/note [Note Number]\'',
            'myid' => 'Get your Line ID',
            'markSchedule' => 'Mark your schedule as done \'/markSchedule [Schedule Number]\'',
            'editNote' => 'Edit your note \'/editNote [Note Number]:[New Title]:[New Content]\'',
            'deleteNote' => 'Delete your note \'/deleteNote [Note Number]\''
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

    public static function pushMessage($text, $line_id)
    {
        $api = LineController::getMessagingApi();
        $message = new TextMessage(['type' => 'text', 'text' => $text]);
        $push = new PushMessageRequest([
            'to' => $line_id,
            'messages' => [$message]
        ]);
        $api->pushMessage($push);
    }

    public function deleteNote($text, $user, $reply_token)
    {
        $idx = intval($text) - 1;
        $replyText = "";
        try {
            if ($user->notes[$idx]) {
                $note = $user->notes[$idx];
                $note->delete();
                $replyText = "Succesfully delete '" . $note->title . "' note! Thankyou! 😊";
            }
        } catch (Exception $e) {
            $replyText = "Oh no, look's like your note cannot be found! 😢";
        }
        return $this->handleSuccessResponse($replyText, $reply_token);
    }

    public function replyMessage($text, $reply_token)
    {
        $api = LineController::getMessagingApi();
        $message = new TextMessage(['type' => 'text', 'text' => $text]);
        $request = new ReplyMessageRequest([
            'replyToken' => $reply_token,
            'messages' => [$message]
        ]);
        $api->replyMessage($request);
    }

    public function login($text, $user, $reply_token, $line_id)
    {
        $data = explode(":", $text);

        $email = $data[0];
        $password = $data[1];
        Log::debug($email . $password);
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = User::where('email', $email)->first();

            $user->line_id = $line_id;
            $user->save();

            $this->replyMessage("Succesfully integrated to Tsk Management 😊!\n\nWelcome, " . $user->name . "\n\n Try to '/help' to see more information.", $reply_token);
            return response()->json('Succesfully response', 200);
        };
        $this->replyMessage("Ups, looks like you're having a wrong credentials!", $reply_token);
        return response()->json('User not found', 400);
    }


    public function note($text, $user, $reply_token)
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
        return $this->handleSuccessResponse($replyText, $reply_token);
    }

    public function myid($text, $user, $reply_token, $line_id)
    {
        return $this->handleSuccessResponse('This is your line id 😊: ' . $line_id, $reply_token);
    }

    public function addSchedule($text, $user, $reply_token)
    {
        $data = $text;

        $replyText = "";
        $schedule = Schedule::create([
            'title' => $data,
            'date' => now(),
            'is_done' => false,
            'scheduleable_id' => $user->id,
            'scheduleable_type' => User::class
        ]);
        if ($schedule) {
            $replyText = "Succesfully create '" . $schedule->title . "' schedule! Thankyou! 😊";
        } else {
            $replyText = "Oh no, look's like the schedule can't be created! 😢";
        }
        return $this->handleSuccessResponse($replyText, $reply_token);
    }

    public function markSchedule($text, $user, $reply_token){
        $replyText = "";
        try {
            $idx = intval($text) - 1;
            if ($user->schedulesToday[$idx]) {
                $schedule = $user->schedulesToday[$idx];
                $schedule->is_done = !$schedule->is_done;
                $schedule->save();
                $replyText = "Successfully marked '" . $schedule->title . "' as " . ($schedule->is_done ? "done" : "undone") . "!";
            }
        } catch (Exception $e) {
            $replyText = "Oh no, look's like your schedule cannot be found! 😢";
        }
        return $this->handleSuccessResponse($replyText, $reply_token);
    }

    public function editNote($text, $user, $reply_token)
    {
        $data = explode(":", $text);

        $idx = intval($data[0]) - 1;
        $title = $data[1];
        $content = $data[2];

        $replyText = "";
        try {
            if ($user->notes[$idx]) {
                $note = $user->notes[$idx];
                $note->title = $title;
                $note->content = $content;
                $note->save();
                $replyText = "Succesfully edit '" . $note->title . "' note! Thankyou! 😊";
            }
        } catch (Exception $e) {
            $replyText = "Oh no, look's like your note cannot be found! 😢";
        }
        return $this->handleSuccessResponse($replyText, $reply_token);
    }

    public function addNote($text, $user, $reply_token)
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
            $replyText = "Succesfully create '" . $title . "' note! Thankyou! 😊";
        } else {
            $replyText = "Oh no, look's like the note can't be created! 😢";
        }
        return $this->handleSuccessResponse($replyText, $reply_token);
    }

    public function notes($text, $user, $reply_token)
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
        return $this->handleSuccessResponse($replyText, $reply_token);
    }

    public function help($text, $line_id, $reply_token)
    {
        $replyText = "Welcome to tsk management Web App 😊, to try our features you can:\n\n";
        foreach ($this->commands as $command => $text) {
            $replyText .= "- " . $this->prefix . $command . " " . $text . "\n";
        }
        $replyText .= "\nAnd you can visit our app on " . env('APP_URL');
        return $this->handleSuccessResponse($replyText, $reply_token);
    }

    public function handleSuccessResponse($replyText, $reply_token)
    {
        $this->replyMessage($replyText, $reply_token);
        return response()->json('Succesfully response', 200);
    }

    public function schedules($text, $user, $reply_token)
    {
        $replyText = "";
        if (count($user->schedulesToday) <= 0) {
            $replyText = "You don't have any schedules today.";
        } else {
            $replyText = "Hello " . $user->name . " 😊\nThis is your schedules for today:\n\n";
            foreach ($user->schedulesToday as $key => $schedule) {
                $done = "";
                if ($schedule->is_done) {
                    $done = "Done";
                } else {
                    $done = "Not Done";
                }
                $replyText .= $key + 1 . ") " . $schedule->title . " - " . $done . "\n";
            }
        }
        return $this->handleSuccessResponse($replyText, $reply_token);
    }

    public function isGuestCommand($command){
        return in_array($command, ['login', 'help']);
    }

    public function mediator($text, $line_id, $reply_token)
    {
        $user = User::where('line_id', $line_id)->first();
        foreach ($this->commands as $command => $commandText) {
            if (Str::startsWith($text, $this->prefix . $command)) {
                if(!$this->isGuestCommand($command) && !$user){
                    return $this->handleAuthenticated($reply_token);
                }
                $parts = explode(' ', $text);
                array_shift($parts);
                $extractedText = trim(implode(' ', $parts));
                return $this->{$command}($extractedText, $user, $reply_token, $line_id);
            }
        }
        return $this->handleUnknownCommand($reply_token);
    }

    public function handleAuthenticated($reply_token)
    {
        $this->replyMessage("I don't know who you are yet 😯, try to '/login'", $reply_token);
        return response()->json('Authenticated', 403);
    }

    public function handleUnknownCommand($reply_token)
    {
        $this->replyMessage("If you're loss, try to '/help' 😊", $reply_token);
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
        $reply_token = $event['replyToken'];
        $user_id = $event['source']['userId'];

        if ($text && $reply_token && $user_id) {
            try {
                $this->mediator($text, $user_id, $reply_token);
            } catch (Exception $e) {
                Log::error($e);
                return $this->handleSuccessResponse("Oh no 😢, look's like you made a terrible thing!", $reply_token);
            }
        } else {
            return response()->json('Error validating data', 404);
        }
    }
}
