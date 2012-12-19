<?php

include_once '../File.php';
include_once '../Uploader.php';
include_once '../transformers/Transformer.php';
include_once '../transformers/TransformerAbstract.php';
include_once '../transformers/CropTransformer.php';
include_once '../transformers/FlipTransformer.php';
include_once '../transformers/ResizeTransformer.php';
include_once '../transformers/ScaleTransformer.php';

function debug($v) {
	echo '<pre>' . print_r($v, true) . '</pre>';
}

if ($_FILES) {
	$upload = new \mjohnson\transit\Uploader($_FILES['file']);
	$upload->setUploadDirectory(__DIR__ . '/tmp/');

	if ($file = $upload->upload()) {
		$file->rename(function($name) {
			return md5($name);
		});

		$t1 = new \mjohnson\transit\transformers\CropTransformer($file, array('width' => 100, 'height' => 100, 'overwrite' => true));
		$file = $t1->transform();

		$t2 = new \mjohnson\transit\transformers\ScaleTransformer($file, array('overwrite' => true));
		$file = $t2->transform();
	}
} ?>

<!DOCTYPE html>
<head>
	<title>Upload Test</title>
</head>
<body>
	<form action="" method="POST" enctype="multipart/form-data">
		<input type="file" name="file">
		<button type="submit">Upload</button>
	</form>
</body>