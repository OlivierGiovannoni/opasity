<?php

function getOrderId($commId)
{
    $sqlComment = "SELECT Commande FROM webcommercial_commentaire WHERE Commentaire_id='$commId';";
    $rowComment = querySQL($sqlComment, $GLOBALS['connection'], true, true);
    $orderId = $rowComment['Commande'];
    return ($orderId);
}

function updatePrevious($prevId)
{
    $sqlComment = "UPDATE webcommercial_commentaire SET DernierCom=1 WHERE Commentaire_id='$prevId';";
    querySQL($sqlComment, $GLOBALS['connection'], false); // UPDATE output doesn't need to be fetched.
}

function deleteComment($commId)
{
    $sqlComment = "DELETE FROM webcommercial_commentaire WHERE Commentaire_id='$commId';";
    querySQL($sqlComment, $GLOBALS['connection'], false); // DELETE output doesn't need to be fetched.
}


function selectPrevious($commId)
{
    $columns = "Commande,DernierCom";
    $sqlComment = "SELECT $columns FROM webcommercial_commentaire WHERE Commentaire_id='$commId';";
    $rowComment = querySQL($sqlComment, $GLOBALS['connection'], true, true);

    $orderId = $rowComment['Commande'];
    $last = $rowComment['DernierCom'];
    if ($last != 1)
        return (-1);
    $sqlComment = "SELECT Commentaire_id FROM webcommercial_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    if ($resultComment = $GLOBALS['connection']->query($sqlComment)) {

        $rowComment = mysqli_fetch_array($resultComment);
        $rowComment = mysqli_fetch_array($resultComment);
        return ($rowComment['Commentaire_id']);
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connection']->error;
    }
}

require_once "helper.php";

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$commId = filter_input(INPUT_GET, "id");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $charset = mysqli_set_charset($connection, "utf8");

    if ($charset === FALSE)
        die("MySQL SET CHARSET error: ". $connection->error);

    $orderId = getOrderId($commId);
    $prevId = selectPrevious($commId);
    deleteComment($commId);
    if ($prevId != -1)
        updatePrevious($prevId);

    header("Location: commentList.php?id=" . $orderId);

    } else
        displayLogin("Veuillez vous connecter.");

    $connection->close();
}

?>
