<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
{
    parent::setUp();

    // Run migrations for in-memory SQLite
    $this->artisan('migrate');

    // Optionally, seed the database if needed
    // $this->artisan('db:seed');
}
}
