<?php

function fetchUsers()
{
    $columns = "id,username,passwordhash,email,fname,lname,created,lastLogin,superuser";
    $sqlUsers = "SELECT $columns FROM webcontrat_utilisateurs;";
    $rowsUsers = querySQL($sqlUsers, $GLOBALS['connection']);

    foreach ($rowsUsers as $rowUser) {

        $id = $rowUser['id'];
        $username = $rowUser['username'];
        $password = $rowUser['passwordhash'];
        $email = $rowUser['email'];
        $fname = $rowUser['fname'];
        $lname = $rowUser['lname'];
        $created = $rowUser['created'];
        $lastLogin = $rowUser['lastLogin'];
        $superuser = ($rowUser['superuser'] == 1 ? "Oui" : "Non");

        $reviewsImage = generateImage("../png/review.png", "Revues", 24, 24);
        $reviewsLink = generateLink("userReviews.php?id=" . $id, $reviewsImage);
        $clientsImage = generateImage("../png/client.png", "Clients", 24, 24);
        $clientsLink = generateLink("userClients.php?id=" . $id, $clientsImage);

        $editImage = generateImage("../png/edit.png", "Modifier", 24, 24);
        $editLink = generateLink("userEdit.php?id=" . $id, $editImage, "_self");
        $deleteImage = generateImage("../png/delete.png", "Supprimer", 24, 24);
        $deleteLink = generateLink("userDelete.php?id=" . $id, $deleteImage, "_self", "return confirm('Supprimer l\'utilisateur: $username ?')");

        $permLinks = $reviewsLink . " " . $clientsLink;
        $editLinks = $editLink . " " . $deleteLink;

        $cells = array($id, $username, $password, $email, $fname, $lname, $created, $lastLogin, $permLinks, $superuser, $editLinks);
        $cells = generateRow($cells);
        foreach($cells as $cell)
            echo $cell;
    }
}

require_once "helper.php";

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged() && isAdmin()) {

        $charset = mysqli_set_charset($connection, "utf8");

        if ($charset === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);

        $style = file_get_contents("../html/search.html");
        $style = str_replace("Recherche {type}: {query}", "Admin", $style);
        echo $style;

        echo "<h2>Liste des utilisateurs</h2>";
        echo "<table>";

        $createImage = generateImage("../png/add.png", "Nouvel utilisateur", 24, 24);
        $createLink = generateLink("../html/userCreate.html", $createImage);
        echo $createLink;

        $cells = array("ID","Nom d'utilisateur","Mot de passe","Email","Prénom","Nom","Crée le","Dernière connexion","Accès","Superuser", "Interagir");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        fetchUsers();

        echo "</table><br><br><br>";
    } else
        header("Location: index.php");

    $connection->close();
}

?>
