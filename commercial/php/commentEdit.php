<?php

function editForm($commId, $style)
{
    $columns = "NumTelephone,AdresseMail,Commentaire,Prochaine_relance,Commande";
    $sqlComment = "SELECT $columns FROM webcommercial_commentaire WHERE Commentaire_id='$commId';";
    $rowComment = querySQL($sqlComment, $GLOBALS['connection'], true, true);

    $phone = $rowComment['NumTelephone'];
    $email = $rowComment['AdresseMail'];
    $comment = $rowComment['Commentaire'];
    $nextDue = $rowComment['Prochaine_relance'];
    $orderId = $rowComment['Commande'];

    if (!isDateValid($nextDue))
        $nextDue = "1970-01-01";
    
    $style = str_replace("{commId}", $commId, $style);
    $style = str_replace("{phone}", $phone, $style);
    $style = str_replace("{email}", $email, $style);
    $style = str_replace("{comment}", $comment, $style);
    $style = str_replace("{nextDue}", $nextDue, $style);
    $style = str_replace("{orderId}", $orderId, $style);
    return ($style);
}

function editComment($commId, $orderId, $phone, $email, $nextDue, $comment)
{
    sanitizeInput($comment);
    $ruleSET = "NumTelephone='$phone',AdresseMail='$email',Prochaine_relance='$nextDue',Commentaire='$comment'";
    $sqlComment = "UPDATE webcommercial_commentaire SET $ruleSET WHERE Commentaire_id='$commId';";
    $rowComment = querySQL($sqlComment, $GLOBALS['connection'], false);
    header("Location: commentList.php?id=" . $orderId);
}

require_once "helper.php";

$credentials = getCredentials("../credentials.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$id = filter_input(INPUT_GET, "id");
$author = getAuthor($id);

$commId = filter_input(INPUT_POST, "commentId");
$orderId = filter_input(INPUT_POST, "orderId");
$phone = filter_input(INPUT_POST, "numPhone");
$email = filter_input(INPUT_POST, "emailAddr");
$nextDueDate = filter_input(INPUT_POST, "nextDueDate");
$unpaidReason = filter_input(INPUT_POST, "unpaidReason");

$unpaidReason = sanitizeInput($unpaidReason);

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged() && (isAuthor($author) || isAdmin())) {

        $charset = mysqli_set_charset($connection, "utf8");

        if ($charset === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);

        $style = file_get_contents("../html/commentEdit.html");
        $filled = (
            isset($commId) &&
            isset($orderId) &&
            isset($phone) &&
            isset($email) &&
            isset($nextDueDate) &&
            isset($unpaidReason));

        if ($filled === false) {

            $style = editForm($id, $style); 
            echo $style;
        } else
            editComment($commId, $orderId, $phone, $email, $nextDueDate, $unpaidReason);
    } else
        header("Location: index.php");

    $connection->close();
}

?>
