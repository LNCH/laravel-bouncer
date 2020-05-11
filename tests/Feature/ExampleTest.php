<?php

namespace Lnch\LaravelBouncer\Tests\Feature;

use Lnch\LaravelBouncer\Tests\TestCase;

class ExampleTest extends TestCase
{
    /** @test */
    public function true_is_not_false(): void
    {
        $this->assertNotFalse(true);
    }
}
