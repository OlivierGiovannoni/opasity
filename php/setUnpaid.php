<?php

$orderId = filter_input(INPUT_POST, "hiddenId");

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

$credsFile = "../credentialsW.txt";
$credentials = credsArr(file_get_contents($credsFile));

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB

function setPaid($orderId)
{
    $sqlPaid = "UPDATE webcontrat_commentaire SET Reglement='' WHERE Commande='$orderId';";
    if ($resultPaid = $GLOBALS['connection']->query($sqlPaid)) {

        // UPDATE output doesn't need to be fetched.
        echo "Le contrat à été passé en 'non-reglé' avec succès. ";
        echo "<a  href=\"../index.php\">Retourner au menu</a>";
    } else {
        echo "Query error: ". $sqlPaid ." // ". $GLOBALS['connection']->error;
    }
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    $charset = mysqli_set_charset($connection, "utf8");

    if ($charset === FALSE)
        die("MySQL SET CHARSET error: ". $connection->error);

    setPaid($orderId);
}

?>
