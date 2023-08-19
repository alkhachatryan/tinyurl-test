<?php

namespace App\Listeners;

use App\Enums\TokenTypeEnum;
use App\Models\User;
use App\Notifications\User\EmailVerificationNotification;
use App\Services\TokenService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailVerificationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(protected TokenService $tokenService)
    {}

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        /** @var User $user */
        $user = $event->user;
        $tokenResource = $this->tokenService->generateToken($user, TokenTypeEnum::EMAIL_VERIFICATION);
        $user->notify(new EmailVerificationNotification($tokenResource));
    }
}
