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

function updatePrevious($prevId)
{
    $sqlComment = "UPDATE webcontrat_commentaire SET DernierCom=1 WHERE Commentaire_id='$prevId';";
    if ($resultComment = $GLOBALS['connectionW']->query($sqlComment)) {

        // UPDATE output doesn't need to be fetched.
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connectionW']->error;
    }
}

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

function selectPrevious($commId)
{
    $sqlComment = "SELECT Commande,DernierCom FROM webcontrat_commentaire WHERE Commentaire_id='$commId';";
    if ($resultComment = $GLOBALS['connectionW']->query($sqlComment)) {

        $rowComment = mysqli_fetch_array($resultComment);
        $orderId = $rowComment['Commande'];
        $last = $rowComment['DernierCom'];
        if ($last != 1)
            return (-1);
        $sqlComment = "SELECT Commentaire_id FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
        if ($resultComment = $GLOBALS['connectionW']->query($sqlComment)) {

            $rowComment = mysqli_fetch_array($resultComment);
            $rowComment = mysqli_fetch_array($resultComment);
            return ($rowComment['Commentaire_id']);
        } else {
            echo "Query error: ". $sqlComment ." // ". $GLOBALS['connectionW']->error;
        }
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connectionW']->error;
    }
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    $charsetW = mysqli_set_charset($connectionW, "utf8");

    if ($charsetW === FALSE)
        die("MySQL SET CHARSET error: ". $connectionW->error);

    $prevId = selectPrevious($commId);
    deleteComment($commId);
    if ($prevId != -1)
        updatePrevious($prevId);

    $connectionW->close();
}

?>
