<?php

namespace Lnch\LaravelBouncer\Tests\Feature;

use Lnch\LaravelBouncer\LaravelBouncerFacade;
use Lnch\LaravelBouncer\Tests\TestCase;

class AuthorisationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        LaravelBouncerFacade::registerGateChecks();
    }

    /** @test */
    public function a_permission_passes_a_gate_check_with_the_can_method(): void
    {
        $this->assertTrue($this->user->can($this->permission->key));
    }

    /** @test */
    public function a_permission_fails_a_gate_check_with_the_can_method(): void
    {
        $this->assertFalse($this->user->can('invalid_permission'));
    }

    /** @test */
    public function a_permission_returns_false_from_a_gate_check_with_the_cannot_method(): void
    {
        $this->assertFalse($this->user->cannot($this->permission->key));
    }

    /** @test */
    public function a_permission_returns_true_from_a_gate_check_with_the_cannot_method(): void
    {
        $this->assertTrue($this->user->cannot('invalid_permission'));
    }
}
