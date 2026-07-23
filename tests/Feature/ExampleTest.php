<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
   
    public function test_guests_are_redirected_to_login(): void
    {
        $this->withoutVite();
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}