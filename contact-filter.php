<?php
$pageTitle = "Contact";
$metaDescription = "Formulaire de contact";

$erreurs = [];
$civilite = '';
$nom = '';
$prenom = '';
$email = '';
$raison = '';
$message = '';
$donnees_formulaire = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // filter_has_var vérifie que la variable existe
    if (!filter_has_var(INPUT_POST, 'civilite') || empty(trim($_POST['civilite']))) {
        $erreurs['civilite'] = "Le champ civilité est obligatoire";
    } else { // si un champ a été rentré, nettoyage (on retire les caractères spéciaux) et validation
        $civilite = filter_input(INPUT_POST, 'civilite', FILTER_SANITIZE_SPECIAL_CHARS);
        $civilites_valides = ['mme', 'mr'];
        if (!in_array($civilite, $civilites_valides)) {
            $erreurs['civilite'] = "La civilité sélectionnée n'est pas valide.";
        }
    }

    if (!filter_has_var(INPUT_POST, 'user_nom') || empty(trim($_POST['user_nom']))) {
        $erreurs['nom'] = "Le champ nom est obligatoire";
    } else {
        $nom = filter_input(INPUT_POST, 'user_nom', FILTER_SANITIZE_SPECIAL_CHARS);
    }

    if (!filter_has_var(INPUT_POST, 'user_prenom') || empty(trim($_POST['user_prenom']))) {
        $erreurs['prenom'] = "Le champ prénom est obligatoire";
    } else {
        $prenom = filter_input(INPUT_POST, 'user_prenom', FILTER_SANITIZE_SPECIAL_CHARS);
    }

    if (!filter_has_var(INPUT_POST, 'user_email') || empty(trim($_POST['user_email']))) {
        $erreurs['email'] = "Le champ email est obligatoire";
    } else {
        $email = filter_input(INPUT_POST, 'user_email', FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            $erreurs['email'] = "L'adresse email n'est pas valide.";
            $email = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL);
            // récupération de la valeur brute pour réaffichage
        }
    }

    if (!filter_has_var(INPUT_POST, 'raison_contact') || empty($_POST['raison_contact'])) {
        $erreurs['raison_contact'] = "Il est obligatoire de nous donner la raison de votre demande.";
    } else {
        $raison = filter_input(INPUT_POST, 'raison_contact', FILTER_SANITIZE_SPECIAL_CHARS);
        $raisons_valides = ['comptabilite', 'informatique'];
        if (!in_array($raison, $raisons_valides)) {
            $erreurs['raison_contact'] = "La raison sélectionnée n'est pas valide.";
        }
    }

    if (!filter_has_var(INPUT_POST, 'user_message') || empty(trim($_POST['user_message']))) {
        $erreurs['message'] = "Le champ message est obligatoire";
    } else {
        $message = filter_input(INPUT_POST, 'user_message', FILTER_SANITIZE_SPECIAL_CHARS);
        $message = trim($message);
        if (strlen($message) < 5) {
            $erreurs['message'] = "Le message doit contenir au moins 5 caractères.";
        }
    }

    if (empty($erreurs)) {
        $donnees_formulaire = "--- Nouvelle soumission de formulaire ---\n";
        $donnees_formulaire .= "Date : " . date("Y-m-d H:i:s") . "\n";
        $donnees_formulaire .= "Civilité : " . $civilite . "\n";
        $donnees_formulaire .= "Nom : " . $nom . "\n";
        $donnees_formulaire .= "Prénom : " . $prenom . "\n";
        $donnees_formulaire .= "Email : " . $email . "\n";
        $donnees_formulaire .= "Raison du contact : " . $raison . "\n";
        $donnees_formulaire .= "Message : " . $message . "\n";
        $donnees_formulaire .= "------------------------------------\n";
    }

    $fichier = "contacts/formulaires_contact.txt";
    $repertoire = dirname($fichier);
    if (!is_dir($repertoire)) {
        mkdir($repertoire);
    }

    $resultat = file_put_contents($fichier, $donnees_formulaire, FILE_APPEND | LOCK_EX);

    if ($resultat === false) {
        $_SESSION['warning_message'] = "Votre message n'a pas été enregistré. Nous revenons vers vous très vite.";
    } else {
        $_SESSION['success_message'] = "Votre message a bien été enregistré.";
    }

    header('Location: frontcontroller.php?page=contact-confirmation');
    exit;
}
