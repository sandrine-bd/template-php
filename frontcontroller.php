<?php

// Récupération du paramètre "page" depuis l'URL
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil'; // à la place de la page accueil, on pourrait choisir 404.

// En-tête commun du site
include 'include/header.php';

// Chargement de la page demandée
switch ($page) {
    case 'accueil':
        include 'pages/accueil.php';
        break;

    case 'a-propos':
        include 'pages/a-propos.php';
        break;

    case 'contact':
        include 'pages/contact.php';
        break;

    case 'contact-confirmation':
        include 'pages/contact-confirmation.php';
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Page introuvable</h1>";
}

// Pied de page commun
include 'include/footer.php';