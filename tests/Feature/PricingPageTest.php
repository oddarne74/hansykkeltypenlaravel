<?php

namespace Tests\Feature;

use Tests\TestCase;

class PricingPageTest extends TestCase
{
    public function test_pricing_page_redirects_to_home(): void
    {
        $this->get(route('pricing'))
            ->assertRedirect('/');
    }

    public function test_legacy_pricing_url_redirects_to_home(): void
    {
        $this->get('/pricing')
            ->assertStatus(301)
            ->assertRedirect('/');
    }
}
