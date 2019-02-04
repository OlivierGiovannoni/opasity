<?php

function fetchUsers()
{
    $sqlUsers = "SELECT * FROM webcontrat_utilisateurs;";
    $rowsUsers = querySQL($sqlUsers, $GLOBALS['connectionW']);

    foreach ($rowsUsers as $rowUser) {

        $id = $rowUser['id'];
        $username = $rowUser['username'];
        $password = $rowUser['passwordhash'];
        $email = $rowUser['email'];
        $fname = $rowUser['fname'];
        $lname = $rowUser['lname'];
        $created = $rowUser['created'];
        $lastLogin = $rowUser['lastLogin'];
        $superuser = $rowUser['superuser'];
        $editImage = generateImage("../png/edit.png", "Modifier", 24, 24);
        $editLink = generateLink("userEdit.php?id=" . $id, $editImage, "_self");
        $deleteImage = generateImage("../png/delete.png", "Supprimer", 24, 24);
        $deleteLink = generateLink("userDelete.php?id=" . $id, $deleteImage, "_self", "return confirm('Supprimer l\'utilisateur: $username ?')");
        $links = $editLink . " " . $deleteLink;

        $cells = array($id, $username, $password, $email, $fname, $lname, $created, $lastLogin, $superuser, $links);
        $cells = generateRow($cells);
        foreach($cells as $cell)
            echo $cell;
    }
}

require_once "helper.php";

$credentialsW = getCredentials("../credentialsW.txt");

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNEXION A LA DB WRITE

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged() && isAdmin()) {

        $style = file_get_contents("../html/search.html");
        $style = str_replace("Recherche {type}: {query}", "Admin", $style);
        echo $style;

        echo "<h2>Liste des utilisateurs</h2>";
        echo "<table>";

        $createImage = generateImage("../png/add.png", "Nouvel utilisateur", 24, 24);
        $createLink = generateLink("../html/userCreate.html", $createImage);
        echo $createLink;

        $cells = array("ID","Nom d'utilisateur","Mot de passe","Email","Prénom","Nom","Crée le","Dernière connexion","Superuser","Interagir");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        fetchUsers();

        echo "</table>";
    } else
        header("Location: index.php");

    $connectionW->close();
}

?>
