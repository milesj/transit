<?php

require_once 'include.php';

if ($_FILES && !empty($_POST['accessKey']) && !empty($_POST['secretKey']) && !empty($_POST['vault'])) {
    $transporter = new Transit\Transporter\Aws\GlacierTransporter($_POST['accessKey'], $_POST['secretKey'], array(
        'vault' => $_POST['vault'],
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
    <title>Transit - Transport Glacier</title>
</head>
<body>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="accessKey" placeholder="Access Key"><br>
        <input type="text" name="secretKey" placeholder="Secret Key"><br>
        <input type="text" name="vault" placeholder="Vault"><br>
        <input type="file" name="file"><br>
        <button type="submit">Upload</button>
    </form>
</body>