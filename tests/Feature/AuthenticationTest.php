<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdminLoginPage()
    {
        $response = $this->get(route('admin.session.create'));

        $response->assertStatus(200);
    }
}
