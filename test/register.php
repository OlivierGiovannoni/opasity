<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$fname = filter_input(INPUT_POST, "fname");
$lname = filter_input(INPUT_POST, "lname");
$username = filter_input(INPUT_POST, "username");
$email = filter_input(INPUT_POST, "email");
$password = filter_input(INPUT_POST, "password");
$pwdrepeat = filter_input(INPUT_POST, "pwdrepeat");

$fname = testInput($fname);
$lname = testInput($lname);
$username = testInput($username);
$email = testInput($email);
$password = testInput($password);
$pwdrepeat = testInput($pwdrepeat);

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

function register($fname, $lname, $username, $email, $password, $pwdrepeat)
{
    if ($password !== $pwdrepeat) {
        echo "Passwords do not match";
        return ;
    }
    $sqlLogin = "SELECT username FROM webcontrat_utilisateurs WHERE username='$username' OR email='$username';";
    if ($resultLogin = $GLOBALS['connectionW']->query($sqlLogin)) {
        $total = mysqli_num_rows($resultLogin);
        if ($total !== 0) {
            echo "User already exists";
            return ;
        }
    } else {
        echo "Query error: ". $sqlLogin ." // ". $GLOBALS['connectionW']->error;
    }
    $pwdhash = password_hash($password, PASSWORD_DEFAULT);
    $created = date("Y-m-d h:i:s");
    $rowNames = "username,passwordhash,email,fname,lname,created,lastLogin";
    $rowValues = "'$username',\"$pwdhash\",'$email','$fname','$lname','$created','$created'";
    $sqlRegister = "INSERT INTO webcontrat_utilisateurs ($rowNames) VALUES ($rowValues);";
    if ($resultRegister = $GLOBALS['connectionW']->query($sqlRegister)) {

        // INSERT output doesn't need to be fetched.
    } else {
        echo "Query error: ". $sqlRegister ." // ". $GLOBALS['connectionW']->error; 
    }
}

register($fname, $lname, $username, $email, $password, $pwdrepeat);

?>
