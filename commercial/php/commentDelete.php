<?php

function getIds($commId)
{
    $columns = "Client_id,Revue_id";
    $sqlIds = "SELECT $columns FROM webcommercial_commentaire WHERE Commentaire_id='$commId';";
    $rowIds = querySQL($sqlIds, $GLOBALS['connection'], true, true);

    $clientId = $rowIds['Client_id'];
    $reviewId = $rowIds['Revue_id'];

    $ids = array('client' => $clientId, 'review' => $reviewId);
    return ($ids);
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
    $columns = "Client_id,Revue_id,DernierCom";
    $sqlComment = "SELECT $columns FROM webcommercial_commentaire WHERE Commentaire_id='$commId';";
    $rowComment = querySQL($sqlComment, $GLOBALS['connection'], true, true);

    $clientId = $rowComment['Client_id'];
    $reviewId = $rowComment['Revue_id'];
    $last = $rowComment['DernierCom'];
    if ($last != 1)
        return (-1);
    $sqlComment = "SELECT Commentaire_id FROM webcommercial_commentaire WHERE Client_id='$clientId' AND Revue_id='$reviewId' ORDER BY Commentaire_id DESC;";
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

    $ids = getIds($commId);
    $clientId = $ids['client'];
    $reviewId = $ids['review'];

    $prevId = selectPrevious($commId);
    deleteComment($commId);
    if ($prevId != -1)
        updatePrevious($prevId);

    header("Location: commentList.php?clientId=" . $clientId . "&reviewId=" . $reviewId);

    } else
        displayLogin("Veuillez vous connecter.");

    $connection->close();
}

?>
