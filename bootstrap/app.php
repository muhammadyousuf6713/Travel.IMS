<?php

/*
|--------------------------------------------------------------------------
| Cross-Platform Case-Insensitive Autoloader Fallback
|--------------------------------------------------------------------------
| Guarantees models with mismatched casing (e.g. App\customer vs Customer.php,
| App\role vs Role.php, App\Notification vs notification.php) resolve
| flawlessly across case-sensitive Linux hosting environments.
*/
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $relative = substr($class, 4);
        $appPath = dirname(__DIR__) . '/app';
        $directFile = $appPath . '/' . str_replace('\\', '/', $relative) . '.php';
        if (is_file($directFile)) {
            require_once $directFile;
        } else {
            $segments = explode('\\', $relative);
            $currentDir = $appPath;
            foreach ($segments as $i => $segment) {
                $isTargetFile = ($i === count($segments) - 1);
                $target = $isTargetFile ? $segment . '.php' : $segment;
                $matched = null;
                if (is_dir($currentDir)) {
                    $files = scandir($currentDir);
                    foreach ($files as $f) {
                        if (strcasecmp($f, $target) === 0) {
                            $matched = $f;
                            break;
                        }
                    }
                }
                if ($matched === null) {
                    return;
                }
                $currentDir .= '/' . $matched;
            }

            if (is_file($currentDir)) {
                require_once $currentDir;
            }
        }

        if (!class_exists($class, false) && !interface_exists($class, false) && !trait_exists($class, false)) {
            $segments = explode('\\', $relative);
            $base = end($segments);
            $prefix = count($segments) > 1 ? implode('\\', array_slice($segments, 0, -1)) . '\\' : '';
            foreach ([$base, ucfirst($base), strtolower($base), lcfirst($base)] as $v) {
                $candidate = 'App\\' . $prefix . $v;
                if (class_exists($candidate, false)) {
                    class_alias($candidate, $class);
                    break;
                }
            }
        }
    }
}, true, true);

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
