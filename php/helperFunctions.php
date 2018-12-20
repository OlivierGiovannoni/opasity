<?php

/*
** ***********************
** ** Generic functions **
** ***********************
*/

/*
** Parameters: String
** Return: String
*/
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return ($data);
}

/*
** Parameters: String
** Return: Array
*/
function getCredentials($credsFile)
{
    $credsStr = file_get_contents($credsFile);
    $credsArr = array();
    $linesArr = explode(";", $credsStr);
    $linesArr = explode("\n", $linesArr[0]);
    foreach ($linesArr as $index => $line) {

        $valueSplit = explode(":", $line);
        $credsArr[$valueSplit[0]] = $valueSplit[1];
    }
    return ($credsArr);
}

/*
** Parameters: String, Object, Bool, Bool
** Return: Array
*/
function querySQL($query, $connection, $results = true, $first = false)
{
    $result = $connection->query($query);
    if ($result && $results === true) {

        if ($first === false)
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        else
            $rows = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return ($rows);
    }
    echo "MySQL query error:<br>Query: " . $sql . "<br>Error: " . $connection->error . "<br>";
    return (NULL);
}

/*
** Parameters: String, String, String, String
** Return: String
*/
function generateInput($type, $name, $id, $value)
{
    $input = "<input type=\"" . $type . "\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\">";
    return ($input);
}

/*
** Parameters: String, String, String, Array
** Return: Array
*/
function generateForm($target, $action, $method, $inputs)
{
    $form = array();
    $formOpen = "<form target=\"" . $target . "\" action=\"" . $action . "\" method=\"" . $method . "\">";
    array_push($form, $formOpen);
    foreach ($inputs as $input) {

        array_push($form, $input);
    }
    $formClose = "</form>";
    array_push($form, $formClose);
    return ($form);
}

/*
** Parameters: String
** Return: String
*/
function getOrderIdShort($orderId)
{
    $orderIdShort = substr($orderId, 2, 2) . substr($orderId, 10, 4);
    return ($orderIdShort);
}

//tmp delete
function generateCell($cell, $header = false)
{
    $open = ($header === true ? "<th>" : "<td>");
    $close = ($header === true ? "</th>" : "</td>");
    $cell = $open . $cell . $close;
    return ($cell);
}

/*
** Parameters: Array, Bool
** Return: Array
*/
function generateRow($cells, $header = false)
{
    $row = array();
    $rowOpen = "<tr>";
    array_push($row, $rowOpen);
    foreach ($cells as $cell) {

        $open = ($header === true ? "<th>" : "<td>");
        $close = ($header === true ? "</th>" : "</td>");
        $cell = $open . $cell . $close;
        array_push($row, $cell);
    }
    $rowClose = "</tr>";
    array_push($row, $rowClose);
    return ($row);

}

/*
** Parameters: String, String, String
** Return: String
*/
function generateLink($href, $text, $target = "_self", $onclick = null)
{
    $link = "<a href=\"" . $href . "\" target=\"" . $target . "\" onclick=\"" . $onclick . "\">" . $text . "</a>";
    return ($link);
}

/*
** Parameters: String, String
** Return: String
*/
function skipAccents($str, $charset = "utf-8")
{
    $str = htmlentities($str, ENT_NOQUOTES, $charset);

    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
    $str = preg_replace('#&[^;]+;#', '', $str);

    $str = str_replace(" ", "_", $str);
    return ($str);
}

/*
** ***************************
** ** App-specific function **
** ***************************
*/

/*
** Parameters: String
** Return: Array
*/
function getOrderDetails($orderId)
{
    $sqlOrder = "SELECT Client_id,PrixHT,Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";
    $rowOrder = querySQL($sqlOrder, $GLOBALS['connectionR']);

    while ($rowOrder) {

        $clientId = $rowOrder['Client_id'];
        $priceRaw = $rowOrder['PrixHT'];
        $sqlClient = "SELECT id,NomSociete,NomContact1 FROM webcontrat_client WHERE id='$clientId';";
        $rowClient = querySQL($sqlClient, $GLOBALS['connectionR'], true, true);
        $companyName = $rowClient['NomSociete'];
        $contactName = $rowClient['NomContact1'];

        $details = array('clientId' => $clientId, 'companyName' => $companyName, 'contactName' => $contactName, 'priceRaw' => $priceRaw);
        return ($details);
    }
}

/*
** Parameters: String
** Return: Array
*/
function findReview($infoId)
{
    $sqlInfoReview = "SELECT Revue_id FROM webcontrat_info_revue WHERE Info_id='$infoId';";
    $rowInfoReview = querySQL($sqlInfoReview, $GLOBALS['connectionR'], true, true);
    $finalId = $rowInfoReview['Revue_id'];
    $sqlReview = "SELECT id,Nom,Annee,Paru FROM webcontrat_revue WHERE id='$finalId';";
    $rowReview = querySQL($sqlReview, $GLOBALS['connectionR'], true, true);
    $finalName = $rowReview['Nom'];
    $finalId = $rowReview['id'];
    $finalYear = $rowReview['Annee'];
    $finalPub = $rowReview['Paru'];
    $final = array('Name' => $finalName, 'Id' => $finalId, 'Year' => $finalYear, 'Pub' => $finalPub);
    return ($final);
}

/*
** Parameters: String
** Return: String
*/
function isItPaid($orderId)
{
    $sqlPaid = "SELECT Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";
    $rowPaid = querySQL($sqlPaid, $GLOBALS['connectionR'], true, true);
    return ($rowPaid['Reglement']);
}

?>
