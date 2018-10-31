<?php

$clientId = filter_input(INPUT_POST, "clientId");
$previousId = filter_input(INPUT_POST, "previousId");
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

function uploadFile($tmpFile, $fileName)
{
    $fileDirectory = "fichiers/";
    $newFile = $fileDirectory . basename($fileName);

    if (move_uploaded_file($tmpFile, $newFile)) {
        echo basename($_FILES['fileUpload']['name']). " à été mis en ligne.";
    } else {
        echo "Il y a eu un problème pendant la mise en ligne du fichier...";
    }
}

function newComment($orderId, $orderIdShort, $phone, $email, $nextDueDate, $unpaidReason, $paidConfirm, $clientId, $previousId)
{
    $orderIdShort = $GLOBALS['orderIdShort'];
    $today = date("Y-m-d");
    $paidBool = ($paidConfirm == "on" ? 1 : 0 );

    $sqlContactInfo = "SELECT Tel FROM webcontrat_client WHERE id='$clientId' ORDER BY DateCreation DESC;";
    if ($resultContactInfo = $GLOBALS['connection']->query($sqlContactInfo)) {

        $rowContactInfo = mysqli_fetch_array($resultContactInfo);

        if ($phone == "")
            $phone = $rowContactInfo['Tel'];
    } else {
        echo "Query error: ". $sqlContactInfo ." // ". $GLOBALS['connection']->error;
    }

    if ($paidBool == 1) {
        $sqlPaid = "UPDATE Reglement='R' FROM webcontrat_contrat WHERE Commande='$orderId';";
        if ($resultPaid = $GLOBALS['connection']->query($sqlPaid)) {

            // UPDATE output doesn't need to be fetched.
        } else {
            echo "Query error: ". $sqlPaid ." // ". $GLOBALS['connection']->error;
        }
    }
    $previousId = ($previousId == "" ? "NULL" : $previousId);

    $rowNames = "Commentaire,Auteur,Date,Commentaire_precedent,Dernier_commentaire,Commande,Commande_courte,Payee,Prochaine_relance,NumTelephone,AdresseMail";
    $rowValues = "\"$unpaidReason\",'dev','$today',$previousId,$paidBool,'$orderId','$orderIdShort',$paidBool,'$nextDueDate','$phone','$email'";
    $sqlNewComment = "INSERT INTO webcontrat_commentaire ($rowNames) VALUES ($rowValues);";
    if ($resultNewComment = $GLOBALS['connection']->query($sqlNewComment)) {

        // INSERT output doesn't need to be fetched.
    } else {
        echo "Query error: ". $sqlNewComment ." // ". $GLOBALS['connection']->error; 
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

    newComment($orderId, $orderIdShort, $phone, $email, $nextDueDate, $unpaidReason, $paidConfirm, $clientId, $previousId);
    uploadFile($_FILES['fileUpload']['tmp_name'], $_FILES['fileUpload']['name']);
    /* echo "</table>"; */
    /* echo "</html>"; */
}

?>
