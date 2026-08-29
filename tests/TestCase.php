<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = require \Illuminate\Foundation\Application::inferBasePath().'/bootstrap/app.php';

        $cachedConfig = $app->getCachedConfigPath();
        if (file_exists($cachedConfig)) {
            @unlink($cachedConfig);
        }

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
