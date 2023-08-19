<?php

namespace Tests\Feature;

use App\Enums\TokenTypeEnum;
use App\Services\TokenService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Tests\Traits\Faker;
use Tests\Traits\UserFlow;

class AuthTest extends TestCase
{
    use DatabaseTransactions;
    use Faker;
    use UserFlow;
    protected TokenService $tokenService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:refresh');
        $this->tokenService = new TokenService();
    }

    public function testUserCanRegisterAndVerifyEmail()
    {
        $userSkeleton = $this->userSkeleton();

        $response = $this->postJson(route('auth.register'), $userSkeleton);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('users', [
            'email' => $userSkeleton['email'],
        ]);
    }

    public function testUserCanVerifyEmail()
    {
        $user = User::factory()->create([
            'email' => $this->faker()->email,
            'email_verified_at' => null,
        ]);

        $token = $this->tokenService->generateToken($user, TokenTypeEnum::EMAIL_VERIFICATION);

        $this->assertDatabaseHas('tokens', [
            'user_id' => $user->id,
            'type' => TokenTypeEnum::EMAIL_VERIFICATION,
        ]);

        $this->postJson(route('auth.verify-email'), [
            'token' => $token->encrypt()
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'email_verified_at' => null,
        ]);

        $this->assertDatabaseMissing('tokens', [
            'user_id' => $user->id,
            'type' => TokenTypeEnum::EMAIL_VERIFICATION,
        ]);
    }

    public function testUserCannotVerifyEmailWithWrongToken()
    {
        $user = User::factory()->create([
            'email' => $this->faker()->email,
            'email_verified_at' => null,
        ]);

        $this->tokenService->generateToken($user, TokenTypeEnum::EMAIL_VERIFICATION);

        $response = $this->postJson(route('auth.verify-email'), [
            'token' => Str::random(64)
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email_verified_at' => null,
        ]);

        $this->assertDatabaseHas('tokens', [
            'user_id' => $user->id,
            'type' => TokenTypeEnum::EMAIL_VERIFICATION,
        ]);
    }

    public function testUserCanLogin()
    {
        $email = $this->faker()->email;

        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('password'),
        ]);

        $this->postJson(route('auth.login'), [
            'email' => $email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function testUserCannotLoginWithWrongCredentials()
    {
        $email = $this->faker()->email;

        User::factory()->create([
            'email' => $email,
            'password' => bcrypt('password'),
        ]);

        $this->postJson(route('auth.login'), [
            'email' => $email,
            'password' => 'password1',
        ]);

        $this->assertGuest();
    }

    public function testUserCanLogout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson(route('auth.logout'));

        $this->assertGuest();
    }

    public function testUserCanRequestPasswordReset()
    {
        $email = 'john@example.com';

        $user = User::factory()->create([
            'email' => $email,
        ]);

        $this->postJson(route('auth.forgot-password'), [
            'email' => $email,
        ]);

        $this->assertDatabaseHas('tokens', [
            'user_id' => $user->id,
            'type' => TokenTypeEnum::PASSWORD_RESET
        ]);
    }

    public function testUserCanResetPassword()
    {
        $user = User::factory()->create();

        $token = $this->tokenService->generateToken($user, TokenTypeEnum::PASSWORD_RESET);

        $response = $this->postJson(route('auth.password-reset'), [
            'token' => $token->encrypt(),
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }
}
