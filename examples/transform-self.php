<?php

require_once 'include.php';

if ($_FILES) {
	$transit = new mjohnson\transit\Transit($_FILES['file']);
	$transit
		->setDirectory(__DIR__ . '/tmp/')
		->addSelfTransformer(new mjohnson\transit\transformers\image\CropTransformer(array('width' => 100, 'height' => 100)))
		->addSelfTransformer(new mjohnson\transit\transformers\image\ScaleTransformer());

	try {
		if ($transit->upload()) {
			if ($transit->transform()) {
				debug($transit->getOriginalFile()->toArray());
			}
		}
	} catch (Exception $e) {
		debug($e->getMessage());
	}
} ?>

<!DOCTYPE html>
<head>
	<title>Transit - Upload + Self Transform</title>
</head>
<body>
	<form action="" method="POST" enctype="multipart/form-data">
		<input type="file" name="file">
		<button type="submit">Upload</button>
	</form>
</body>