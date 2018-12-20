<?php

REMOVE THIS SHIT($credsStr)
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

 REMOVE "../credentialsW.txt";
$credentialsW = getCredentials("../credentialsW.txt"));

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']);

function getAllOrders()
{
    $sqlOrders = "SELECT Commande FROM webcontrat_commentaire;";
    if ($resultOrders = $GLOBALS['connectionW']->query($sqlOrders)) {

        $orders = array();
        while ($rowOrders = mysqli_fetch_array($resultOrders)) {

            $check = in_array($rowOrders['Commande'], $orders);
            if ($check === FALSE)
                array_push($orders, $rowOrders['Commande']);
            else
                continue ;
        }
        return ($orders);
    } else {
        echo "Query error: ". $sqlOrders ." // ". $GLOBALS['connectionW']->error;
    }
    return (NULL);
}

function updater($val, $id)
{
    $sqlUpdate = "UPDATE webcontrat_commentaire SET DernierCom=$val WHERE Commentaire_id=$id;";
    if ($resultUpdate = $GLOBALS['connectionW']->query($sqlUpdate)) {

        //UPDATE ain't need no fetcha boi
    } else {
        echo "Query error: ". $sqlUpdate ." // ". $GLOBALS['connectionW']->error;
    }

}

function changeLast()
{
    $orders = getAllOrders();
    foreach ($orders as $order) {

        $sqlHelper = "SELECT Commentaire_id,DernierCom FROM webcontrat_commentaire WHERE Commande='$order' ORDER BY Commentaire_id DESC;";
        if ($resultHelper = $GLOBALS['connectionW']->query($sqlHelper)) {

            $rowHelper = mysqli_fetch_array($resultHelper);
            updater(1, $rowHelper['Commentaire_id']);
            while ($rowHelper = mysqli_fetch_array($resultHelper))
                updater(0, $rowHelper['Commentaire_id']);
        } else {
            echo "Query error: ". $sqlHelper ." // ". $GLOBALS['connectionW']->error;
        }
    }
}

mysqli_set_charset($connectionW, "utf8");
changeLast();
//unlink(__FILE__);
$connectionW->close();

?>
