<?php

// Récupération du paramètre "page" depuis l'URL
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil'; // à la place de la page accueil, on pourrait choisir 404.

// En-tête commun du site
include 'header.php';

// Chargement de la page demandée
switch ($page) {
    case 'accueil':
        include 'accueil.php';
        break;

    case 'a-propos':
        include 'a-propos.php';
        break;

    case 'contact':
        include 'contact.php';
        break;

    case 'contact-filter':
        include 'contact-filter.php';
        break;

    case 'contact-confirmation':
        include 'contact-confirmation.php';
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Page introuvable</h1>";
}

// Pied de page commun
include 'footer.php';