<?php

// Toutes les requêtes passent par ce fichier qui charge la page demandée via le paramètre GET "page".

// Inclusion des fichiers de configuration si nécessaire
// require_once 'config/config.php

// Récupération du paramètre "page" depuis l'URL
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil'; // à la place de la page accueil, on pourrait choisir 404.

// En-tête commun du site
include 'header.php';

// Chargement de la page demandée
switch ($page) {
    case 'accueil':
        include 'accueil.php';
        break;

    case 'about':
        include 'a-propos.php';
        break;

    case 'erreur404':
        include 'erreur404.php';
        break;
}

// Pied de page commun
include 'footer.php';