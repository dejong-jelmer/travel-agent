<?php

namespace Tests\Feature;

use Tests\TestCase;

class RevealAnimationsTest extends TestCase
{
    public function test_layout_switches_the_reveal_animations_on_by_config(): void
    {
        config(['app.reveal_animations' => true]);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('<html lang="nl" class="scroll-smooth" data-reveal="on">', false);
    }

    public function test_layout_switches_the_reveal_animations_off_by_config(): void
    {
        config(['app.reveal_animations' => false]);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('<html lang="nl" class="scroll-smooth" data-reveal="off">', false);
    }
}
