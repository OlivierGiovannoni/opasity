<?php

$orderId = filter_input(INPUT_POST, "hiddenId");
$orderIdShort = filter_input(INPUT_POST, "hiddenIdShort");
$phone = filter_input(INPUT_POST, "numPhone");
$email = filter_input(INPUT_POST, "emailAddr");
$nextDueDate = filter_input(INPUT_POST, "nextDueDate");
$unpaidReason = filter_input(INPUT_POST, "unpaidReason");
$paidConfirm = filter_input(INPUT_POST, "paidConfirm");
//$darkBool = filter_input(INPUT_POST, "darkBool");

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
$dbname = "opas";

$connection = new mysqli($host, $dbusername, $dbpassword, $dbname); // CONNEXION A LA DB

function newComment($orderId, $orderIdShort, $phone, $email, $nextDueDate, $unpaidReason, $paidConfirm)
{
    $orderIdShort = $GLOBALS['orderIdShort'];

    $today = date("Y-m-d");
    $sqlNewComment = "INSERT INTO webcontrat_commentaire (Commentaire,Auteur,Payee,Date,Dernier_commentaire,Commande,Commande_courte,NumTelephone,AdresseMail) VALUES (\"$unpaidReason\",\"auteur\",0,\"$today\",1,\"OPGI00004A4468\",\"GI4468\",\"$phone\",\"$email\");";
    if ($resultNewComment = $GLOBALS['connection']->query($sqlNewComment)) {

        /* while ($rowNewComment = mysqli_fetch_array($resultNewComment)) { */

        /* } */
    } else {
        echo "Query error: ". $sqlNewComment ." // ". $GLOBALS['connection']->error; 
    }
    if ($paidConfirm == "on") {
        $sqlPaid = "UPDATE Reglement='R' FROM webcontrat_contrat WHERE Commande='$orderId';";
        if ($resultPaid = $GLOBALS['connection']->query($sqlPaid)) {

            /* while ($rowPaid = mysqli_fetch_array($resultPaid)) { */

            /* } */
        } else {
            echo "Query error: ". $sqlPaid ." // ". $GLOBALS['connection']->error;
            echo "IUZHEIFUL $nextDueDate";
        }
    }
    $GLOBALS['connection']->close();
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    /* $style = file_get_contents("allComments.html"); */

    /* if ($darkBool == "true") */
    /*     $style = str_replace("commentLight.css", "commentDark.css", $style); */

    /* $style = str_replace("{order}", $orderIdShort, $style); */

    /* echo $style; */
    /* echo "<i><h1>Fiche: " . $orderIdShort . "</h1></i>"; */
    /* echo "<table style=\"width:100%\">"; */
    /* echo "<tr>"; */
    /* echo "</tr>"; */

    newComment($orderId, $orderIdShort, $phone, $email, $nextDueDate, $unpaidReason, $paidConfirm);

    /* echo "</table>"; */
    /* echo "</html>"; */
}

?>
