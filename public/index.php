<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

/**
 * Custom request class to fix Symfony base URL detection bug when running Laravel
 * in a subfolder with route caching enabled, or when accessing without /public.
 */
class CustomRequest extends Request
{
    protected function prepareBaseUrl(): string
    {
        $scriptName = $this->server->get('SCRIPT_NAME', '');
        $requestUri = $this->getRequestUri();
        
        // Find the base path of the project (e.g. /supa)
        if (str_contains($scriptName, '/public/index.php')) {
            $projectBase = strstr($scriptName, '/public/index.php', true);
        } elseif (str_contains($scriptName, '/index.php')) {
            $projectBase = strstr($scriptName, '/index.php', true);
        } else {
            $projectBase = parent::prepareBaseUrl();
        }
        
        $projectBase = $projectBase === false ? '' : rtrim($projectBase, '/');
        
        // If the request URI explicitly contains the "/public" segment right after the project base path,
        // then the base URL for this request should include "/public".
        // Otherwise, it should only be the project base path.
        if ($projectBase !== '') {
            if (str_starts_with($requestUri, $projectBase . '/public/') || $requestUri === $projectBase . '/public') {
                return $projectBase . '/public';
            }
            return $projectBase;
        }
        
        if (str_starts_with($requestUri, '/public/') || $requestUri === '/public') {
            return '/public';
        }
        
        return '';
    }
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(CustomRequest::capture());
