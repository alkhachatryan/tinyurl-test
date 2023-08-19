<?php

namespace App\Services;

use App\Enums\TokenTypeEnum;
use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class TokenService
{
    public function validateToken(string $encryptedToken, TokenTypeEnum $type, bool $andDelete = true): int|false
    {
        [$userId, $token] = Crypt::decrypt($encryptedToken);

        $query = Token::whereUserId($userId)
            ->whereToken($token)
            ->whereType($type)
            ->whereDate('expires_at', '>=', now());

        if($andDelete) {
            return $query->delete() ? $userId : false;
        } else {
            return $query->exists() ? $userId : false;
        }
    }

    public function generateToken(User $user, TokenTypeEnum $type, int $length = 64, int $ttl = 60): Token
    {
        Token::whereUserId($user->id)->whereType($type)->delete();

        return Token::create([
            'user_id'    => $user->id,
            'token'      => Str::random($length),
            'type'       => $type,
            'expires_at' => now()->addMinutes($ttl)
        ]);
    }
}
