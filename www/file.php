<?php

$data = file_get_contents($_FILES['fileUpload']['tmp_name']);
$data = base64_decode($data);
file_put_contents("file.pdf", $data);

?>