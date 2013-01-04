<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/php/transit
 */

error_reporting(E_ALL | E_STRICT);

// Test constants
define('TEST_DIR', __DIR__);
define('TEMP_DIR', TEST_DIR . '/tmp');
define('VENDOR_DIR', dirname(TEST_DIR) . '/vendor');

define('AWS_ACCESS', '');
define('AWS_SECRET', '');
define('S3_BUCKET', '');
define('S3_REGION', 'us-east-1');
define('GLACIER_VAULT', '');
define('GLACIER_REGION', 'us-east-1');

// Ensure that composer has installed all dependencies
if (!file_exists(VENDOR_DIR . '/autoload.php')) {
	exit('Please install composer before running tests!');
}

// Include the composer autoloader
$loader = require VENDOR_DIR . '/autoload.php';
$loader->add('Transit', TEST_DIR);