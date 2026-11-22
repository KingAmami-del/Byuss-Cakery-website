<?php

namespace Tests\Feature;

use Tests\TestCase;

class BirthdayCakePageTest extends TestCase
{
    public function test_birthday_cake_page_has_an_empty_gallery_and_custom_whatsapp_link(): void
    {
        $this->get(route('birthday-cake'))
            ->assertOk()
            ->assertSee('Birthday Cake')
            ->assertSee('Foto-foto Birthday Cake buatan admin akan ditampilkan di sini.')
            ->assertSee('Custom Cake')
            ->assertSee('https://wa.me/6285979754307', false)
            ->assertSee('target="_blank"', false);
    }

    public function test_old_how_to_order_url_redirects_to_birthday_cake_page(): void
    {
        $this->get('/cara-pesan')
            ->assertRedirect(route('birthday-cake'));
    }
}
