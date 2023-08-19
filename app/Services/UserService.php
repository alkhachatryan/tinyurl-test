<?php

namespace App\Services;

use App\Enums\TokenTypeEnum;
use App\Models\Token;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function verifyUserEmailNow(int $userId): void
    {
        $this->verifyUserEmail($userId, now());
    }

    public function verifyUserEmail(int $userId, string $dateTime): void
    {
        User::whereId($userId)->update(['email_verified_at' => $dateTime]);
        Token::whereUserId($userId)->whereType(TokenTypeEnum::EMAIL_VERIFICATION)->delete();
    }

    public function register(array $data, ?string $eventToFire = Registered::class): User
    {
        if(isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = User::create($data);

        if(!is_null($eventToFire)) {
            event(new $eventToFire($user));
        }

        return $user;
    }

    public function edit(User $user, array $payload): User
    {
        $user->fill($payload);
        $user->save();
        return $user;
    }
}
