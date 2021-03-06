<?php
use Cake\Core\Configure;

// @codingStandardsIgnoreFile
$findRoot = function () {
    $root = __DIR__;
    for ($i = 0; $i < 3; $i++) {
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    }
};

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', $findRoot());
define('APP_DIR', 'App');
define('WEBROOT_DIR', 'webroot');
define('APP', ROOT . '/tests/App/');
define('CONFIG', ROOT . '/tests/config/');
define('WWW_ROOT', ROOT . DS . WEBROOT_DIR . DS);
define('TESTS', ROOT . DS . 'tests' . DS);
define('TMP', ROOT . DS . 'tmp' . DS);
define('LOGS', TMP . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . '/vendor/cakephp/cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);
require ROOT . '/vendor/autoload.php';
require CORE_PATH . 'config/bootstrap.php';

$Loader = (new josegonzalez\Dotenv\Loader(CONFIG . ".env"))
    ->parse()
    ->putenv(true);

Configure::write('App', ['namespace' => 'App']);
Configure::write('debug', true);

$TMP = new \Cake\Filesystem\Folder(TMP);
$TMP->create(TMP . 'cache/models', 0777);
$TMP->create(TMP . 'cache/persistent', 0777);
$TMP->create(TMP . 'cache/views', 0777);
$cache = [
    'default' => [
        'engine' => 'File'
    ],
    '_cake_core_' => [
        'className' => 'File',
        'prefix' => 'cake_queuesadilla_myapp_cake_core_',
        'path' => CACHE . 'persistent/',
        'serialize' => true,
        'duration' => '+10 seconds'
    ],
    '_cake_model_' => [
        'className' => 'File',
        'prefix' => 'cake_queuesadilla_my_app_cake_model_',
        'path' => CACHE . 'models/',
        'serialize' => 'File',
        'duration' => '+10 seconds'
    ]
];
Cake\Cache\Cache::config($cache);
Cake\Core\Configure::write('Session', [
    'defaults' => 'php'
]);

Cake\Core\Plugin::load('SAThomsen/FacebookAuth', ['path' => ROOT . DS, 'autoload' => true]);
// Ensure default test connection is defined
if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite:///:memory:');
}

Cake\Datasource\ConnectionManager::config('test', [
    'url' => getenv('DATABASE_TEST_URL'),
    // 'url' => 'mysql://localhost:3306/databaseName?user=root&password=pass'
    'timezone' => 'UTC',
]);

// Configure facebook
Configure::write('facebook.appId', getenv('FACEBOOK_APP_ID'));
Configure::write('facebook.appSecret', getenv('FACEBOOK_APP_SECRET'));
Configure::write('facebook.graphVersion', getenv('FACEBOOK_API_VERSION'));
Configure::write('facebook.fields', 'id,name,first_name,middle_name,last_name,gender,email');

// Add access token
Configure::write('facebook.token', getenv('FACEBOOK_TOKEN'));
Configure::write('facebook.identifier', getenv('FACEBOOK_IDENTIFIER'));