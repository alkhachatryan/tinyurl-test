<?php

namespace Tests;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @inheritDoc
     */
    public function actingAs(Authenticatable $user, $guard = null)
    {
        parent::actingAs($user, $guard);

        $token = auth()->login($user);
        $this->withHeaders(array_merge([$this->defaultHeaders, ['Authorization' => 'Bearer ' . $token]]));

        return $this;
    }
}
