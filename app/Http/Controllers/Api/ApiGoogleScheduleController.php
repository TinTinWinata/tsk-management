<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ApiGoogleScheduleRequest;
use App\Models\GoogleSchedule;
use App\Models\Schedule;
use App\Models\User;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;

use Google\Client;
use Google\Service\Calendar;
use Illuminate\Support\Facades\Log;

class ApiGoogleScheduleController extends ApiController
{
    protected static $client;
    public const GOOGLE_INVALID_TOKEN = 'Invalid Google Token';

    public static function getClient() {
        if(!ApiGoogleScheduleController::$client) {
            ApiGoogleScheduleController::$client = new Client();
            ApiGoogleScheduleController::$client->setAuthConfig(storage_path('app/google/google-credentials.json'));
            ApiGoogleScheduleController::$client->addScope(Calendar::CALENDAR);
        }
        return ApiGoogleScheduleController::$client;
    }

    private static function convertToGoogleDateTime(string $date, string $modify = null): string {
        $dateTime = new DateTime($date, new DateTimeZone('UTC'));
        if ($modify) {
            $dateTime->modify($modify); // Adjust the date if needed (e.g., +1 hour for end time)
        }
        return $dateTime->format(DateTime::ATOM); // Outputs ISO 8601 format: YYYY-MM-DDTHH:MM:SSZ
    }

    public static function delete(GoogleSchedule $googleSchedule) {
        Log::debug('[Delete Google Schedule] Deleting...', [$googleSchedule]);
        $service = new Calendar(ApiGoogleScheduleController::$client);
        $service->events->delete('primary', $googleSchedule->google_event_id);
        $googleSchedule->delete();
        Log::debug('[Delete Google Schedule] Successfully Deleted!');
    }

    public static function create(Schedule $schedule, User $user) {
        Log::debug('[Create Google Schedule] Creating...', [$schedule]);
        $service = new Calendar(ApiGoogleScheduleController::$client);
        $event = new Calendar\Event([
            'summary' => $schedule->title,
            'location' => null,
            'description' => null,
            'start' => [
                'dateTime' => ApiGoogleScheduleController::convertToGoogleDateTime($schedule->date),
                'timeZone' => 'UTC',
            ],
            'end' => [
                'dateTime' => ApiGoogleScheduleController::convertToGoogleDateTime($schedule->date),
                'timeZone' => 'UTC',
            ],
        ]);

        $calendarId = 'primary';
        $event = $service->events->insert($calendarId, $event);
        $eventId = $event->getId();

        // Saving google schedule
        $data = $schedule->toArray();
        $data['user_id'] = $user->id;
        $data['google_event_id'] = $eventId;
        $data['schedule_id'] = $schedule->id;

        GoogleSchedule::create($data);
        Log::debug('[Create Google Schedule] Successfully Created!');
    }

    public static function update(GoogleSchedule $googleSchedule, Schedule $schedule) {
        Log::debug('[Update Google Schedule] Updating...', [$schedule]);

        if($googleSchedule->title == $schedule->title) {
            Log::debug('[Update Google Schedule] Return! It\'s the same!');
            return;
        }

        $service = new Calendar(ApiGoogleScheduleController::$client);
        $event = $service->events->get('primary', $googleSchedule->google_event_id);
        $event->setSummary($schedule->title);
        $service->events->update('primary', $event->getId(), $event);

        $googleSchedule->title = $schedule->title;
        $googleSchedule->save();
        Log::debug('[Update Google Schedule] Successfully Updated!');
    }

    public static function sync(Schedule $schedule, User $user) {
        Log::debug('[Sync Google Schedule] Start Sync!', [$schedule]);
        $googleSchedule = GoogleSchedule::where('schedule_id', $schedule->id)->first();

        if($googleSchedule) {
            ApiGoogleScheduleController::update($googleSchedule, $schedule);
            return;
        }

        ApiGoogleScheduleController::create($schedule, $user);
        Log::debug('[Sync Google Schedule] Finish Sync!');
    }

    public static function setAccessToken($user){
        if(empty($user)) {
            $user = Auth::user();
        }
        ApiGoogleScheduleController::getClient()->setAccessToken($user->google_access_token);
    }

    public static function checkAccessToken($user): bool {
        if(empty($user)) {
            $user = Auth::user();
        }

        if(!$user->google_access_token) {
            Log::debug('[Check Access Token] No google token found');
            return false;
        }

        return true;
    }

    public function store(ApiGoogleScheduleRequest $request) {
        $user = Auth::user();

        $data = $request->validated();
        $schedule = Schedule::where('id', $data['schedule_id'])->first();

        if(!$schedule) {
            return $this->sendError("Schedule not found", [], 404);
        }

        if(!$user->google_access_token) {
            return $this->sendError(ApiGoogleScheduleController::GOOGLE_INVALID_TOKEN, [], 406);
        }

        ApiGoogleScheduleController::setAccessToken($user);
        ApiGoogleScheduleController::sync($schedule, $user);
        return $this->sendResponse([], "Succesfully create google schedule!");
    }
}
