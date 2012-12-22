<?php

error_reporting(E_ALL | E_STRICT);

require_once '../vendor/autoload.php';
include_once '../src/Transit.php';
include_once '../src/File.php';
include_once '../src/validators/Validator.php';
include_once '../src/validators/AbstractValidator.php';
include_once '../src/validators/ImageValidator.php';
include_once '../src/transformers/Transformer.php';
include_once '../src/transformers/AbstractTransformer.php';
include_once '../src/transformers/image/AbstractImageTransformer.php';
include_once '../src/transformers/image/CropTransformer.php';
include_once '../src/transformers/image/FlipTransformer.php';
include_once '../src/transformers/image/ResizeTransformer.php';
include_once '../src/transformers/image/ScaleTransformer.php';
include_once '../src/transporters/Transporter.php';
include_once '../src/transporters/AbstractTransporter.php';
include_once '../src/transporters/S3Transporter.php';
include_once '../src/exceptions/TransformationException.php';
include_once '../src/exceptions/TransportationException.php';

function debug($v) {
	echo '<pre>' . print_r($v, true) . '</pre>';
}
