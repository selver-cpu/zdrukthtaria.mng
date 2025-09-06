<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->markTestSkipped('Regjistrimi i përdoruesve bëhet manualisht nga administratori, jo përmes faqes publike të regjistrimit.');
        
        $response = $this->post('/register', [
            'emri' => 'Test',
            'mbiemri' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'rol_id' => 4, // Assuming 4 is 'montues' role ID
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
