<?php

require_once 'include.php';

if ($_FILES) {
	$upload = new \mjohnson\transit\handlers\UploadHandler($_FILES['file']);
	$upload->setDirectory(__DIR__ . '/tmp/');

	if ($file = $upload->upload()) {
		debug($file->toArray());

		$t1 = new \mjohnson\transit\transformers\ResizeTransformer($file, array('width' => 100, 'height' => 100));
		$t1f = $t1->transform();
		debug($t1f->toArray());

		$t2 = new \mjohnson\transit\transformers\FlipTransformer($file);
		$t2f = $t2->transform();
		debug($t2f->toArray());
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