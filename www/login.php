<?php

$namesArr = array();
$valuesArr = array();

foreach ($_POST as $name => $value) {

    array_push($namesArr, $name);
    if (!empty($value)) {
        array_push($valuesArr, $value);
    } else {
        array_push($valuesArr, "NULL");
    }
}

$allNames = implode(", ", $namesArr);
$allValues = implode("','", $valuesArr);

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
//$dbname = "OPAS";
$dbname = "tests";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

if (mysqli_connect_error()) {
    die('Connect Error ('. mysqli_connect_errno() .') ' . mysqli_connect_error());
} else {
    $sql = "INSERT INTO users ($allNames) VALUES ('$allValues')";
    if ($conn->query($sql)) {
        echo "New record is inserted sucessfully";
    } else {
        echo "Error: ". $sql ." // ". $conn->error;
    }
    $conn->close();
}

?>
