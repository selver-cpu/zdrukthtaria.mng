<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $this->markTestSkipped('Email verification not implemented in custom schema');
    }

    public function test_email_can_be_verified(): void
    {
        $this->markTestSkipped('Email verification not implemented in custom schema');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $this->markTestSkipped('Email verification not implemented in custom schema');
    }
}
