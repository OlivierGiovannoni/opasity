<?php

$orderId = filter_input(INPUT_POST, "hiddenId");
$orderIdShort = filter_input(INPUT_POST, "hiddenIdShort");
$paidStr = filter_input(INPUT_POST, "hiddenPaid");
$comment = filter_input(INPUT_POST, "comment");
$darkBool = filter_input(INPUT_POST, "darkBool");
$lastId = filter_input(INPUT_POST, "commentId");
$clientId = NULL;

$host = "localhost";
$dbusername = "root";
$dbpassword = "stage972";
$dbname = "opas";

$connection = new mysqli($host, $dbusername, $dbpassword, $dbname); // CONNEXION A LA DB

function generateInput($htmlFileName, $orderId, $orderIdShort, $lastId, $clientId)
{
    $htmlFileData = file_get_contents($htmlFileName);
    $htmlFileData = str_replace("{orderId}", $orderId, $htmlFileData);
    $htmlFileData = str_replace("{orderIdShort}", $orderIdShort, $htmlFileData);
    $htmlFileData = str_replace("{lastId}", $lastId, $htmlFileData);
    $htmlFileData = str_replace("{clientId}", $clientId, $htmlFileData);
    echo $htmlFileData;
}

function getContactName($orderId)
{
    $sqlContactId = "SELECT Client_id FROM webcontrat_contrat WHERE Commande='$orderId' ORDER BY DateEmission DESC;";
    if ($resultContactId = $GLOBALS['connection']->query($sqlContactId)) {

        $rowContactId = mysqli_fetch_array($resultContactId);
        $contactId = $rowContactId['Client_id'];
        $sqlContactName = "SELECT NomContact1,NomSociete FROM webcontrat_client WHERE id='$contactId' ORDER BY DateCreation DESC;";
        if ($resultContactName = $GLOBALS['connection']->query($sqlContactName)) {

            $rowContactName = mysqli_fetch_array($resultContactName);
            $contactName = $rowContactName['NomSociete'];
            return (array('id' => $contactId, 'name' => $contactName));
        } else {
            echo "Query error: ". $sqlContactName ." // ". $GLOBALS['connection']->error;
        }
    } else {
        echo "Query error: ". $sqlContactId ." // ". $GLOBALS['connection']->error;
    }
}


function listComments()
{
    $orderIdShort = $GLOBALS['orderIdShort'];
    $sqlComment = "SELECT Commentaire_id,Commentaire,Auteur,Date,AdresseMail,NumTelephone,Prochaine_relance,Payee FROM webcontrat_commentaire WHERE Commande_courte='$orderIdShort' ORDER BY Commentaire_id DESC;";
    if ($resultComment = $GLOBALS['connection']->query($sqlComment)) {

        while ($rowComment = mysqli_fetch_array($resultComment)) {

            $contact = getContactName($GLOBALS['orderId']);
            echo "<tr><td>" . $rowComment['Commentaire'] . "</td>";
            echo "<td>" . $rowComment['Auteur'] . "</td>";
            echo "<td>" . date("d/m/Y", strtotime($rowComment['Date'])) . "</td>";

            /* $clientForm = "<form action=\"clientOrders.php\" method=\"post\">"; */
            /* /\* $darkBool = "<input type=\"hidden\" name=\"darkBool\" value=\"" . $GLOBALS['darkBool'] . "\">"; *\/ */
            /* /\* $getPaidOrders = "<input type=\"hidden\" name=\"hiddenPaid\" value=\"" . $GLOBALS['getPaid'] . "\">"; *\/ */
            /* $clientHidden = "<input type=\"hidden\" name=\"clientId\" value=\"" . $contact['id'] . "\">"; */
            /* $clientInput = "<input type=\"submit\" id=\"tableSub\" name=\"clientName\" value=\"" . $contact['name'] . "\">"; */
            /* $closeForm = "</form>"; */
            /* echo "<td>" . $clientForm . /\* $darkBool . $getPaidOrders . *\/$clientHidden . $clientInput . $closeForm . "</td>"; */

            echo "<td>" . $contact['name'] . "</td>";
            $mailHref = "<a id=\"tableSub\" href=\"mailto:" . $rowComment['AdresseMail'] . "\">" . $rowComment['AdresseMail'] . "</a>";
            echo "<td>" . $mailHref . "</td>";
            echo "<td>" . $rowComment['NumTelephone'] . "</td>";
            echo "<td>" . date("d/m/Y", strtotime($rowComment['Prochaine_relance'])) . "</td>";
            echo "<td>" . $rowComment['Payee'] . "</td></tr>";
        }
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connection']->error;
    }
    $GLOBALS['connection']->close();
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    $style = file_get_contents("allComments.html");

    if ($darkBool == "true")
        $style = str_replace("commentLight.css", "commentDark.css", $style);

    $style = str_replace("{order}", $orderIdShort, $style);

    echo $style;
    echo "<i><h1>Fiche: " . $orderIdShort . "</h1></i>";
    echo "<table style=\"width:100%\">";
    echo "<tr>";
    echo "<th>Commentaire</th>";
    echo "<th>Auteur</th>";
    echo "<th>Date commentaire</th>";
    echo "<th>Nom de l'entreprise</th>";
    echo "<th>E-mail</th>";
    echo "<th>Téléphone</th>";
    echo "<th>Prochaine relance</th>";
    echo "<th>Payé</th>";
    echo "</tr>";

    if (mysqli_set_charset($connection, "utf8") === TRUE) {

        listComments();
        if ($paidStr == "")
            generateInput("addComment.html", $orderId, $orderIdShort, $lastId, $clientId);
    }
    else
        die("MySQL SET CHARSET error: ". $connection->error);

    echo "</table>";
    echo "</html>";
}

?>
