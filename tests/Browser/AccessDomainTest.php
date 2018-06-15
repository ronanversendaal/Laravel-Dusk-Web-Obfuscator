<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AccessDomainTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     * 
     * @group access
     *
     * @return void
     */
    public function testAccessToDomain()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://google.com')
                ->assertSee('Google');
        });
    }
}
