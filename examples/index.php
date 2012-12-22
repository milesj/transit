<?php

require_once 'include.php';

use mjohnson\transit\Transit;
use mjohnson\transit\transformers\image\CropTransformer;
use mjohnson\transit\transporters\S3Transporter;
use mjohnson\transit\validators\ImageValidator;
use \Exception;

if ($_FILES) {
	$validator = new ImageValidator();
	$validator
		->addRule('size', 'File size is too large', 2003000);
		//->addRule('height', 'Invalid height', 100);

	$transit = new Transit($_FILES['file']);
	$transit
		->setDirectory(__DIR__ . '/tmp/')
		->setValidator($validator)
		//->setTransporter(new S3Transporter())
		->addTransformer(new CropTransformer(array('width' => 100)));

	try {
		if ($transit->upload()) {
			$transit->getOriginalFile()->rename(function($name) {
				return md5($name);
			});

			if ($transit->transform()) {
				debug($transit->getAllFiles());
			}
		}
	} catch (Exception $e) {
		debug($e->getMessage());
	}
} ?>

<!DOCTYPE html>
<head>
	<title>Transit - Upload</title>
</head>
<body>
<form action="" method="POST" enctype="multipart/form-data">
	<input type="file" name="file">
	<button type="submit">Upload</button>
</form>
</body>