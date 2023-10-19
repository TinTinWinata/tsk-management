<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ScheduleController;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $monthDatas = null;

        $token = null;
        $user = null;

        if (auth()->check()) {
            $monthDatas = ScheduleController::getAvailableMonths();
            $request->user()->tokens()->delete();
            $token = $request->user()->createToken('TSK_API')->plainTextToken;
            $user = $request->user();
            $user['token'] = $token;
        }
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'monthData' => $monthDatas,
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
