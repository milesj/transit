<?php

error_reporting(E_ALL | E_STRICT);

require_once '../vendor/autoload.php';
include_once '../src/Transit.php';
include_once '../src/File.php';
include_once '../src/Validator/Validator.php';
include_once '../src/Validator/AbstractValidator.php';
include_once '../src/Validator/ImageValidator.php';
include_once '../src/Transformer/Transformer.php';
include_once '../src/Transformer/AbstractTransformer.php';
include_once '../src/Transformer/Image/AbstractImageTransformer.php';
include_once '../src/Transformer/Image/CropTransformer.php';
include_once '../src/Transformer/Image/FlipTransformer.php';
include_once '../src/Transformer/Image/ResizeTransformer.php';
include_once '../src/Transformer/Image/ScaleTransformer.php';
include_once '../src/Transporter/Transporter.php';
include_once '../src/Transporter/AbstractTransporter.php';
include_once '../src/Transporter/Aws/AbstractAwsTransporter.php';
include_once '../src/Transporter/Aws/S3Transporter.php';
include_once '../src/Transporter/Aws/GlacierTransporter.php';
include_once '../src/Exception/TransformationException.php';
include_once '../src/Exception/TransportationException.php';

function debug($v) {
	echo '<pre>' . print_r($v, true) . '</pre>';
}
