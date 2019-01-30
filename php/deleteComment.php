<?php

function getOrderId($commId)
{
    $sqlComment = "SELECT Commande FROM webcontrat_commentaire WHERE Commentaire_id='$commId';";
    $rowComment = querySQL($sqlComment, $GLOBALS['connectionW'], true, true);
    $orderId = $rowComment['Commande'];
    return ($orderId);
}

function updatePrevious($prevId)
{
    $sqlComment = "UPDATE webcontrat_commentaire SET DernierCom=1 WHERE Commentaire_id='$prevId';";
    $rowComment = querySQL($sqlComment, $GLOBALS['connectionW'], false); // UPDATE output doesn't need to be fetched.
}

function deleteComment($commId)
{
    $sqlComment = "DELETE FROM webcontrat_commentaire WHERE Commentaire_id='$commId';";
    $rowComment = querySQL($sqlComment, $GLOBALS['connectionW'], false); // DELETE output doesn't need to be fetched.
}


function selectPrevious($commId)
{
    $sqlComment = "SELECT Commande,DernierCom FROM webcontrat_commentaire WHERE Commentaire_id='$commId';";
    $rowComment = querySQL($sqlComment, $GLOBALS['connectionW'], true, true);

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
}

require_once "helperFunctions.php";

$credentialsW = getCredentials("../credentialsW.txt");

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNEXION A LA DB WRITE

$commId = filter_input(INPUT_GET, "id");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    $charsetW = mysqli_set_charset($connectionW, "utf8");

    if ($charsetW === FALSE)
        die("MySQL SET CHARSET error: ". $connectionW->error);

    $orderId = getOrderId($commId);
    $prevId = selectPrevious($commId);
    deleteComment($commId);
    if ($prevId != -1)
        updatePrevious($prevId);

    header("Location: allComments.php?id=" . $orderId);

    $connectionW->close();
}

?>
