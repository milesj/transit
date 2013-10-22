<?php

require_once 'include.php';

if ($_FILES && !empty($_POST['accessKey']) && !empty($_POST['secretKey']) && !empty($_POST['bucket'])) {
    $transporter = new Transit\Transporter\Aws\S3Transporter($_POST['accessKey'], $_POST['secretKey'], array(
        'bucket' => $_POST['bucket'],
        'region' => Aws\Common\Enum\Region::US_EAST_1
    ));

    $transit = new Transit\Transit($_FILES['file']);
    $transit
        ->setDirectory(__DIR__ . '/tmp/')
        ->setTransporter($transporter);

    try {
        if ($transit->upload()) {
            debug($transit->transport());
        }
    } catch (Exception $e) {
        debug($e->getMessage());
    }
} ?>

<!DOCTYPE html>
<head>
    <title>Transit - Transport S3</title>
</head>
<body>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="accessKey" placeholder="Access Key"><br>
        <input type="text" name="secretKey" placeholder="Secret Key"><br>
        <input type="text" name="bucket" placeholder="Bucket"><br>
        <input type="file" name="file"><br>
        <button type="submit">Upload</button>
    </form>
</body>