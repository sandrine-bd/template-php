<!-- header.php -->
<?php
// on utilise les variables définies dans chaque page
if (!isset($pageTitle)) {
    $pageTitle = "Mon site web"; // Titre par défaut
}
if (!isset($metaDescription)) {
    $metaDescription = "Description générale de mon site web"; // Description par défaut
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Site Web</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header> <!-- Navigation et autres éléments communs -->
        <h1>Bienvenue sur mon site web</h1>
        <nav>
            <ul>
                <li><a href="frontcontroller.php?page=accueil">Accueil</a></li>
                <li><a href="frontcontroller.php?page=about">À propos</a></li>
            </ul>
        </nav>
    </header>
