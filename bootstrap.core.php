<?php
/*
|--------------------------------------------------------------------------
| PIMF core bootstrap used for unit testing
|--------------------------------------------------------------------------
*/
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__) . DS);
}

require_once 'autoload.core.php';
require_once 'utils.php';

\Pimf\Config::load(
    array(

        /*
        |------------------------------------------------------------------------
        | The default environment mode for your application [testing|production]
        |------------------------------------------------------------------------
        */
        'environment' => 'testing',
        /*
        |------------------------------------------------------------------------
        | The default timezone of your application.
        | Supported timezones list: http://www.php.net/manual/en/timezones.php
        |------------------------------------------------------------------------
        */
        'timezone' => 'UTC',
        /*
        |--------------------------------------------------------------------------
        | Is it regular HTTP or secure HTTPS
        |--------------------------------------------------------------------------
        */
        'ssl' => false,
        /*
        |------------------------------------------------------------------------
        | Application meta
        |------------------------------------------------------------------------
        */
        'app' => array(

            'name' => 'MyFirstBlog',
            // secret application key or try out http://randomkeygen.com
            'key' => 'some5secret5key5here',
            // the name of the fallback controller
            'default_controller' => 'blog',
            // get cleaner URLs or not
            'routeable' => true,
            // do you have a representational state transfer app?
            'restfull' => false,
            // URL used to access your application without a trailing slash.
            'url' => 'http://localhost',
            // if using mod_rewrite to get cleaner URLs let it empty otherwise set index.php
            'index' => '',
            // the base URL used for your application's asset files
            'asset_url' => '',
        ),
        /*
        |------------------------------------------------------------------------
        | Production environment settings
        |------------------------------------------------------------------------
        */
        'production' => array(
            'db' => array(
                'driver' => 'sqlite',
                'database' => 'app/MyFirstBlog/_database/blog-production.db'
            ),
        ),
        /*
        |------------------------------------------------------------------------
        | Production environment settings
        |------------------------------------------------------------------------
        */
        'testing' => array(
            'db' => array(
                'driver' => 'sqlite',
                'database' => 'app/MyFirstBlog/_database/blog-production.db'
            ),
        ),
        /*
        |------------------------------------------------------------------------
        | Bootstrapping meta
        |------------------------------------------------------------------------
        */
        'bootstrap' => array(
            'local_temp_directory' => '/tmp/'
        ),
        /*
        |------------------------------------------------------------------------
        | Settings for the error handling behavior
        |------------------------------------------------------------------------
        */
        'error' => array(

            'ignore_levels' => array(0),
            'debug_info' => true,
            'log' => true,
        ),
        /*
        |--------------------------------------------------------------------------
        | Session settings
        |--------------------------------------------------------------------------
        */
        'session' => array(

            // Session storage 'cookie', 'file', 'pdo', 'memcached', 'apc', 'redis',
            // 'dba', 'wincache', 'memory'  or '' for non
            'storage' => 'memory',
            // If using file storage - default is null
            'storage_path' => 'app/MyFirstBlog/_session/',
            // If using the PDO (database) session storage
            'database' => array(
                'driver' => 'sqlite',
                'database' => 'app/MyFirstBlog/_session/blog-session.db',
            ),
            // Garbage collection has a 2% chance of occurring for any given request to
            // the application. Feel free to tune this to your requirements.
            'garbage_collection' => array(2, 100),
            // Session lifetime number of minutes
            'lifetime' => 60,
            // Session expiration on web browser close
            'expire_on_close' => false,
            // Session cookie name
            'cookie' => 'pimf_session',
            // Session cookie path
            'path' => '/',
            // Domain for which the session cookie is available.
            'domain' => null,
            // If the cookie should only be sent over HTTPS.
            'secure' => false,
        ),
        /*
        |--------------------------------------------------------------------------
        | Cache settings
        |--------------------------------------------------------------------------
        */
        'cache' => array(

            // Cache storage 'pdo', 'file', 'memcached', 'apc', 'redis', 'dba',
            // 'wincache', 'memory' or '' for non
            'storage' => 'memory',
            // If using file storage - default is null
            'storage_path' => 'app/MyFirstBlog/_cache/',
            // If using the PDO (database) cache storage
            'database' => array(
                'driver' => 'sqlite',
                'database' => 'app/MyFirstBlog/_cache/blog-cache.db',
            ),
            // If using Memcached and APC to prevent collisions with other applications on the server.
            'key' => 'pimfmaster',
            // Memcached servers - for more check out: http://memcached.org
            'memcached' => array(
                'servers' => array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
            ),
        ),

    )

);


$env = new \Pimf\Environment($_SERVER);
$envData = $env->data();

\Pimf\Logger::setup(
    $env->getIp(),
    $envData->get('PHP_SELF', $envData->get('SCRIPT_NAME'))
);

\Pimf\Util\Header\ResponseStatus::setup(
    $envData->get('SERVER_PROTOCOL', 'HTTP/1.0'));

\Pimf\Util\Header::setup(
    $env->getUserAgent()
);

\Pimf\Url::setup($env->getUrl(), $env->isHttps());
\Pimf\Uri::setup($env->PATH_INFO, $env->REQUEST_URI);
\Pimf\Util\Uuid::setup($env->getIp(), $env->getHost());

unset($env, $envData);
