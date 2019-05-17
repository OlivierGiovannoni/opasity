<?php

function getFiles($orderId)
{
    $orderIdShort = getOrderIdShort($orderId);
    $receipts1 = glob("\\\\srv4-adm\\AS400\\FACTURES\\$orderIdShort*.pdf");
    $receipts2 = glob("//srv4-adm/AS400/FACTURES/$orderIdShort*.pdf");
    print_r($receipts1);
    print_r($receipts2);
    die();
    foreach ($receipts as $receipt) {

        $receiptName = basename($receipt);
        $receiptLink = generateLink($receipt, $receiptName);
        echo $receiptLink . "<br>";
    }

    $fullpath = "./files/" . $orderId . "/";
    $files = scandir($fullpath);

    foreach ($files as $file) {

        if ($file === "." || $file === "..")
            continue ;
        $file = sanitizeInput($file);
        $file = stripslashes($file);
        $fileLink = generateLink($fullpath . $file, $file);
        echo $fileLink . "<br>";
    }
}

require_once "helper.php";

session_start();

$orderId = filter_input(INPUT_GET, "id");

getFiles($orderId);

?>
