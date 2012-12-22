<?php

error_reporting(E_ALL | E_STRICT);

require_once '../vendor/autoload.php';
include_once '../src/Transit.php';
include_once '../src/File.php';
include_once '../src/Validator.php';
include_once '../src/handlers/AbstractHandler.php';
include_once '../src/handlers/UploadHandler.php';
include_once '../src/handlers/ImportHandler.php';
include_once '../src/transformers/Transformer.php';
include_once '../src/transformers/AbstractTransformer.php';
include_once '../src/transformers/image/AbstractImageTransformer.php';
include_once '../src/transformers/image/CropTransformer.php';
include_once '../src/transformers/image/FlipTransformer.php';
include_once '../src/transformers/image/ResizeTransformer.php';
include_once '../src/transformers/image/ScaleTransformer.php';

function debug($v) {
	echo '<pre>' . print_r($v, true) . '</pre>';
}