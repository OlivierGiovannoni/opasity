<?php

$fileDirectory = "fichiers/";
$data = file_get_contents($_FILES['fileUpload']['tmp_name']);
$newFile = $fileDirectory;

if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], $newFile)) {
    echo basename($_FILES['fileUpload']['name']). " was successfully uploaded.";
} else {
    echo "Something went wrong while uploading your file...";
}
/* $data = base64_decode($data); */
/* file_put_contents("file.pdf", $data); */

?>