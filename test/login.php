<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$username = filter_input(INPUT_POST, "username");
$password = filter_input(INPUT_POST, "password");

$username = testInput($username);
$password = testInput($password);

function credsArr($credsStr)
{
    $credsArr = array();
    $linesArr = explode(";", $credsStr);
    $linesArr = explode("\n", $linesArr[0]);
    foreach ($linesArr as $index => $line) {

        $valueSplit = explode(":", $line);
        $credsArr[$valueSplit[0]] = $valueSplit[1];
    }
    return ($credsArr);
}

$credsFileW = "../credentialsW.txt";
$credentialsW = credsArr(file_get_contents($credsFileW));

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNEXION A LA DB WRITE

function login($username, $password)
{
    $sqlLogin = "SELECT passwordhash FROM webcontrat_utilisateurs WHERE username='$username' OR email='$username';";
    if ($resultLogin = $GLOBALS['connectionW']->query($sqlLogin)) {
        $total = mysqli_num_rows($resultLogin);
        if ($total === 0) {
            echo "User doesn't exist";
            return ;
        }
        $rowLogin = mysqli_fetch_array($resultLogin);
        $check = password_verify($password, $rowLogin['passwordhash']);
        if ($check === FALSE) {
            echo "Invalid password";
            return ;
        }
        $now = date("Y-m-d h:i:s");
        $sqlRefresh = "UPDATE webcontrat_utilisateurs SET lastLogin='$now' WHERE username='$username';";
        if ($resultRefresh = $GLOBALS['connectionW']->query($sqlRefresh)) {

            // UPDATE output doesn't need to be fetched.
        } else {
            echo "Query error: ". $sqlRefresh ." // ". $GLOBALS['connection']->error;
        }
        echo "Login success";
    } else {
        echo "Query error: ". $sqlLogin ." // ". $GLOBALS['connectionW']->error;
    }
}

login($username, $password);

?>