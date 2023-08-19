<?php

namespace Tests\Traits;

use App\Models\User;

trait UserFlow
{
    protected function userSkeleton(): array
    {
        return [
            'email' => $this->faker()->email,
            'password' => 'qwerqwer',
            'password_confirmation' => 'qwerqwer',
            'name' => $this->faker()->name,
        ];
    }

    protected function createUser(): User
    {
        $userSkeleton = $this->userSkeleton();
        $this->postJson(route('auth.register'), $userSkeleton)->getContent();

        $user = User::whereEmail($userSkeleton['email'])->first();
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }

    protected function loginDummyUser(): User
    {
        $user = $this->createUser();
        $this->actingAs($user);

        return $user;
    }
}
