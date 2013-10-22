<?php

error_reporting(E_ALL | E_STRICT);

function debug($v) {
    echo '<pre>' . print_r($v, true) . '</pre>';
}

require_once '../vendor/autoload.php';
