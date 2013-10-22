<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     http://opensource.org/licenses/mit-license.php
 * @link        http://milesj.me/code/php/transit
 */

error_reporting(E_ALL | E_STRICT);

function env($key, $default = '') {
    foreach (array($_ENV, $_SERVER) as $global) {
        if (isset($global[$key])) {
            return $global[$key];
        }
    }

    return $default;
}

// Test constants
define('TEST_DIR', __DIR__);
define('TEMP_DIR', TEST_DIR . '/tmp');
define('VENDOR_DIR', dirname(TEST_DIR) . '/vendor');

define('AWS_ACCESS', env('AWS_S3_KEY'));
define('AWS_SECRET', env('AWS_S3_SECRET'));
define('S3_BUCKET', env('AWS_S3_BUCKET'));
define('S3_REGION', env('AWS_S3_REGION', 'us-east-1'));
define('GLACIER_VAULT', env('AWS_GLACIER_VAULT'));
define('GLACIER_REGION', env('AWS_GLACIER_REGION', 'us-east-1'));

// Ensure that composer has installed all dependencies
if (!file_exists(VENDOR_DIR . '/autoload.php')) {
    exit('Please install Composer in Transit\'s root folder before running tests!');
}

// Include the composer autoloader
$loader = require VENDOR_DIR . '/autoload.php';
$loader->add('Transit', TEST_DIR);