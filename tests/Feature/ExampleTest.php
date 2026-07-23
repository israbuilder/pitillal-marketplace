<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
   public function test_landing_page_is_accessible(): void
{
    
    $response = $this->get('/');

    $response->assertOk();
}
    public function test_guests_are_redirected_to_login(): void
    {
       
        $response = $this->get('/app');

        $response->assertRedirect(route('login'));
    }
}