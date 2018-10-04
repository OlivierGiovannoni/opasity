<?php

$namesArr = array();
$valuesArr = array();

foreach ($_POST as $name => $value) {
    array_push($namesArr, $name);
    array_push($valuesArr, $value);

}

echo "ALL NAMES UNFILTERED " . implode(", ", $namesArr) . "<br>";
echo "ALL VALUES UNFILTERED " . implode(",", $valuesArr) . "<br>";

$username = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');

if (!empty($username) && !empty($password)) {

    $host = "localhost";
    $dbusername = "root";
    $dbpassword = "stage972";
    //$dbname = "OPAS";
    $dbname = "tests";

    $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

    if (mysqli_connect_error()){
        die('Connect Error ('. mysqli_connect_errno() .') ' . mysqli_connect_error());
    } else {
        $sql = "INSERT INTO generic (username, password) values ('$username','$password')";
        if ($conn->query($sql)) {
            echo "New record is inserted sucessfully";
            echo ;
        } else {
            echo "Error: ". $sql ." // ". $conn->error;
        }
        $conn->close();
    }
} else {
    echo "Username or password should not be empty";
    die();
}

?>
