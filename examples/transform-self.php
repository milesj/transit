<?php

require_once 'include.php';

if ($_FILES) {
    $transit = new Transit\Transit($_FILES['file']);
    $transit
        ->setDirectory(__DIR__ . '/tmp/')
        ->addSelfTransformer(new Transit\Transformer\Image\CropTransformer(array('width' => 100, 'height' => 100)))
        ->addSelfTransformer(new Transit\Transformer\Image\ScaleTransformer());

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