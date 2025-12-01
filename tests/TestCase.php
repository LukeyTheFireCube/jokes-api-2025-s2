<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (empty($this->seeded)) {
            $this->seeded = true;
            $this->seed();
        }
    }
}
