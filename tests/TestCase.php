<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        // Never load a cached local/hosted database configuration into tests.
        // This runs before Laravel boots and before RefreshDatabase can migrate.
        $cache = dirname(__DIR__).'/bootstrap/cache/testing-config-not-used.php';
        foreach (['APP_CONFIG_CACHE' => $cache, 'APP_ENV' => 'testing', 'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => ':memory:', 'DB_URL' => ''] as $key => $value) {
            putenv($key.'='.$value);
            $_ENV[$key] = $_SERVER[$key] = $value;
        }
        $app = parent::createApplication();
        if ($app['config']->get('database.default') !== 'sqlite'
            || $app['config']->get('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Tests must use SQLite :memory:; refusing to run database tests.');
        }

        return $app;
    }
}
