<?php

require_once 'include.php';

if ($_FILES) {
	$transit = new mjohnson\transit\Transit($_FILES['file']);
	$transit
		->setDirectory(__DIR__ . '/tmp/')
		->addTransformer(new mjohnson\transit\transformers\image\ResizeTransformer(array('width' => 100, 'height' => 100)))
		->addTransformer(new mjohnson\transit\transformers\image\FlipTransformer());

	try {
		if ($transit->upload()) {
			if ($transit->transform()) {
				foreach ($transit->getAllFiles() as $file) {
					debug($file->toArray());
				}
			}
		}
	} catch (Exception $e) {
		debug($e->getMessage());
	}
} ?>

<!DOCTYPE html>
<head>
	<title>Transit - Upload + Transforms</title>
</head>
<body>
	<form action="" method="POST" enctype="multipart/form-data">
		<input type="file" name="file">
		<button type="submit">Upload</button>
	</form>
</body>