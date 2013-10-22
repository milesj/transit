<?php

require_once 'include.php';

use Transit\Transit;
use Transit\Transformer\Image\CropTransformer;
use Transit\Transporter\Aws\S3Transporter;
use Transit\Validator\ImageValidator;

if ($_FILES) {
    $validator = new ImageValidator();
    $validator->addRule('size', 'File size is too large', 2003000);

    $transit = new Transit($_FILES['file']);
    $transit
        ->setDirectory(__DIR__ . '/tmp/')
        ->setValidator($validator)
        ->setTransporter(new S3Transporter('access', 'secret', array('bucket' => '', 'region' => '')))
        ->addTransformer(new CropTransformer(array('width' => 100)));

    try {
        if ($transit->upload()) {
            $transit->getOriginalFile()->rename(function($name) {
                return md5($name);
            });

            if ($transit->transform()) {
                debug($transit->getAllFiles());
                debug($transit->transport());
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