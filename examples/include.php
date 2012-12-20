<?php

error_reporting(E_ALL | E_STRICT);

include_once '../File.php';
include_once '../Transit.php';
include_once '../Uploader.php';
include_once '../Importer.php';
include_once '../Validator.php';
include_once '../transformers/Transformer.php';
include_once '../transformers/TransformerAbstract.php';
include_once '../transformers/CropTransformer.php';
include_once '../transformers/FlipTransformer.php';
include_once '../transformers/ResizeTransformer.php';
include_once '../transformers/ScaleTransformer.php';

function debug($v) {
	echo '<pre>' . print_r($v, true) . '</pre>';
}