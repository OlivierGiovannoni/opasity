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
            echo "L'utilisateur n'existe pas";
            return ;
        }
        $rowLogin = mysqli_fetch_array($resultLogin);
        /* $check = password_verify($password, $rowLogin['passwordhash']); // Check if password hash corresponds */
        /* if ($check === FALSE) { // If not, throw */
        if ($password !== $rowLogin['passwordhash']) {
            echo "Mot de passe incorrect";
            return ;
        }
        $now = date("Y-m-d h:i:s");
        $sqlRefresh = "UPDATE webcontrat_utilisateurs SET lastLogin='$now' WHERE username='$username';";
        if ($resultRefresh = $GLOBALS['connectionW']->query($sqlRefresh)) {

            // UPDATE output doesn't need to be fetched.
        } else {
            echo "Query error: ". $sqlRefresh ." // ". $GLOBALS['connection']->error;
        }
        setcookie("author", $username, time() + 3600, "/");
        header("Location: /index.php");
    }
}

login($username, $password);

?>
