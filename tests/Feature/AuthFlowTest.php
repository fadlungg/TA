<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login page renders successfully.
     */
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('sipektatu');
    }

    /**
     * Test that submitting empty login form redirects to dashboard and sets default username.
     */
    public function test_submitting_empty_login_redirects_to_dashboard_with_default_username(): void
    {
        $response = $this->post('/login', []);

        $response->assertRedirect('/dashboard');
        $this->assertEquals(true, session('admin_logged_in'));
        $this->assertEquals('sipektatu', session('admin_username'));
    }

    /**
     * Test that submitting custom username works.
     */
    public function test_submitting_custom_username_redirects_to_dashboard_with_custom_username(): void
    {
        $response = $this->post('/login', [
            'username' => 'fadlun_admin',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertEquals(true, session('admin_logged_in'));
        $this->assertEquals('fadlun_admin', session('admin_username'));
    }

    /**
     * Test that unauthenticated user cannot access dashboard and is redirected to login.
     */
    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
    }

    /**
     * Test that authenticated user can access the dashboard.
     */
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_username' => 'sipektatu',
        ])->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Selamat datang,');
        $response->assertSee('Admin sipektatu');
    }

    /**
     * Test that logout clears session and redirects to login.
     */
    public function test_logout_clears_session_and_redirects_to_login(): void
    {
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_username' => 'sipektatu',
        ])->post('/logout');

        $response->assertRedirect('/login');
        $this->assertNull(session('admin_logged_in'));
        $this->assertNull(session('admin_username'));
    }
}
