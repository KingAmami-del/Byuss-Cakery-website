<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminLoginPageTest extends TestCase
{
    public function test_admin_login_page_is_rendered(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertOk();
        $response->assertSee('Masuk dengan akun admin untuk mengelola toko.');
    }

    public function test_guest_is_redirected_to_login_from_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }
}
