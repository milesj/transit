<?php

error_reporting(E_ALL | E_STRICT);

include_once '../src/File.php';
include_once '../src/AbstractHandler.php';
include_once '../src/Uploader.php';
include_once '../src/Importer.php';
include_once '../src/Validator.php';
include_once '../src/transformers/Transformer.php';
include_once '../src/transformers/AbstractTransformer.php';
include_once '../src/transformers/CropTransformer.php';
include_once '../src/transformers/FlipTransformer.php';
include_once '../src/transformers/ResizeTransformer.php';
include_once '../src/transformers/ScaleTransformer.php';

function debug($v) {
	echo '<pre>' . print_r($v, true) . '</pre>';
}