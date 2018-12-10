<?php

$commId = filter_input(INPUT_POST, "commId");

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

function deleteComment($commId)
{
    $sqlComment = "DELETE FROM webcontrat_commentaire WHERE Commentaire_id='$commId';";
    if ($resultComment = $GLOBALS['connectionW']->query($sqlComment)) {

        // DELETE output doesn't need to be fetched.
        echo "Le commentaire à été supprimé avec succès. ";
        echo "<a  href=\"../index.php\">Retourner au menu</a>";
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connectionW']->error;
    }
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    echo "<meta charset=\"utf-8\">";

    $charsetW = mysqli_set_charset($connectionW, "utf8");

    if ($charsetW === FALSE)
        die("MySQL SET CHARSET error: ". $connectionW->error);

    deleteComment($commId);
}

?>
