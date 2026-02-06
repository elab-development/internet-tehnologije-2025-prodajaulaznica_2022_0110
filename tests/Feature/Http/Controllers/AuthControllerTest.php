<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AuthController
 */
final class AuthControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function register_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AuthController::class,
            'register',
            \App\Http\Requests\AuthRegisterRequest::class
        );
    }

    #[Test]
    public function register_saves_and_responds_with(): void
    {
        $response = $this->get(route('auths.register'));

        $response->assertOk();
        $response->assertJson($user, 201);

        $this->assertDatabaseHas(users, [ /* ... */ ]);
    }


    #[Test]
    public function login_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AuthController::class,
            'login',
            \App\Http\Requests\AuthLoginRequest::class
        );
    }

    #[Test]
    public function login_responds_with(): void
    {
        $response = $this->get(route('auths.login'));

        $response->assertOk();
    }


    #[Test]
    public function logout_responds_with(): void
    {
        $response = $this->get(route('auths.logout'));

        $response->assertOk();
    }


    #[Test]
    public function me_responds_with(): void
    {
        $response = $this->get(route('auths.me'));

        $response->assertOk();
        $response->assertJson($user);
    }
}
