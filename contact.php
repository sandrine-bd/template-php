<?php
$pageTitle = "Contact";
$metaDescription = "Formulaire de contact";

// Initialisation du tableau d'erreurs
$erreurs = [];

// Vérification que le formulaire a bien été soumis via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Initialisation des variables pour éviter les erreurs de portée
    $nom = '';
    $prenom = '';
    $email = '';
    $raison = '';
    $message = '';
    $civilite = '';

    // Validation de la civilité
    if (empty($_POST['civilite'])) {
        $erreurs['civilite'] = 'Veuillez sélectionner une civilité';
    } else {
        $civilite = $_POST['civilite'];
        $civilites_valides = ['mme', 'mr'];
        if (!in_array($civilite, $civilites_valides)) {
            $erreurs['civilite'] = "La civilité sélectionnée n'est pas valide";
        }
    }

    // Validation du nom
    if (empty($_POST['user_nom'])) {
        $erreurs['nom'] = 'Le nom est obligatoire';
    } else {
        // Nettoyage et validation du nom
        $nom = trim(htmlspecialchars($_POST['user_nom'])); // htmlspecialchars() permet d'échapper les caractères spéciaux
        if (strlen($nom) < 1) {
            $erreurs['nom'] = "Le nom ne peut pas être vide";
        }
    }

    // Validation du prénom
    if (empty($_POST['user_prenom'])) {
        $erreurs['prenom'] = 'Le prenom est obligatoire';
    } else {
        $prenom = trim(htmlspecialchars($_POST['user_prenom'])); // nettoyage des données avec trim() pour retirer les espaces inutiles
        if (strlen($prenom) < 1) {
            $erreurs['prenom'] = "La prenom ne peut pas être vide";
        }
    }

    // Validation de l'email
    if (empty($_POST['user_email'])) {
        $erreurs['email'] = "L'email est obligatoire";
    } else {
        $email = trim($_POST['user_email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = "L'email est invalide";
        }
    }

    // Validation de la raison du contact
    if (empty($_POST['raison_contact'])) {
        $erreurs['raison_contact'] = "Veuillez sélectionner une raison de contact";
    } else {
        $raison = $_POST['raison_contact'];
        $raisons_valides = ['comptabilite', 'informatique'];
        if (!in_array($raison, $raisons_valides)) {
            $erreurs['raison_contact'] = "La raison sélectionnée n'est pas valide";
        }
    }

    // Validation du message
    if (empty($_POST['user_message'])) {
        $erreurs['message'] = "Le message est obligatoire";
    } else {
        $message = trim(htmlspecialchars($_POST['user_message']));
        if (strlen($message) < 5) {
            $erreurs['message'] = "Le message doit contenir au moins 5 caractères";
        }
    }

    // Traitement du formulaire si aucune erreur
    if (empty($erreurs)) {
        // Création d'un fichier texte (chaîne formatée) avec les données du formulaire
        $donnees_formulaire = "---Nouvelle soumission de formulaire ---\n";
        $donnees_formulaire .= "Date: " . date("Y-m-d H:i:s") . "\n";
        $donnees_formulaire .= "Civilité: " . $civilite . "\n";
        $donnees_formulaire .= "Nom: " . $nom . "\n";
        $donnees_formulaire .= "Prenom: " . $prenom . "\n";
        $donnees_formulaire .= "Email: " . $email . "\n";
        $donnees_formulaire .= "Message: " . $message . "\n";
        $donnees_formulaire .= "---------------------------------------\n";

        // Définition du chemin du fichier
        $fichier = "contacts/formulaires_contact.txt";

        // Création du dossier s'il n'existe pas
        $repertoire = dirname($fichier);
        if (!is_dir($repertoire)) {
            mkdir($repertoire, 0755, true); // création récursive avec permissions
        }

        // Enregistrement des données dans le fichier grâce aux drapeaux file_append (ajout sans écraser le fichier) et lock (empêche quiconque d'écrire dans le fichier)
        $resultat = file_put_contents($fichier, $donnees_formulaire, FILE_APPEND | LOCK_EX);

        // Vérification que l'enregistrement a fonctionné
        if (!$resultat === false) {
            $_SESSION['warning_message'] = "Votre message a été reçu mais n'a pas pu être enregistré. Nous vous contacterons dès que possible.";
        } else {
            $_SESSION['success_message'] = "Votre message a bien été envoyé. Nous vous répondrons dans les plus bref délais.";
        }

        // Redirection avec message de succès
        header('Location: frontcontroller.php?page=contact_confirmation');
    } else {
        // en cas d'erreur, on les stocke en session pour les afficher sur le formulaire
        $_SESSION['form_errors'] = $erreurs;
        // on conserve aussi les données valides saisies pour éviter à l'utilisateur de tout remplir à nouveau
        $_SESSION['form_data'] = [
            'civilite' => $civilite,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'raison_contact' => $raison,
            'message' => $message,
        ];
        // Redirection vers le formulaire avec les erreurs
        header('Location: frontcontroller.php?page=contact');
        exit;
    }
}

?>

<!-- Affichage des erreurs s'il y en a -->
<?php if (!empty($_SESSION['form_errors'])) : ?>
    <div>
        <ul>
            <?php foreach ($_SESSION['form_errors'] as $error) : ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    unset($_SESSION['form_errors']); // nettoie les erreurs après affichage
    ?>
<?php endif; ?>

<!-- Affichage du message de succès -->
<?php if(isset($_SESSION['success_message'])): ?>
    <div>
        <?php echo $_SESSION['success_message']; ?>
    </div>
    <?php
    unset($_SESSION['success_message']); // nettoie le message après affichage
    ?>
<?php endif; ?>

    <!-- Affichage du message d'avertissement -->
<?php if (isset($_SESSION['warning_message'])): ?>
    <div>
        <?php echo $_SESSION['warning_message']; ?>
    </div>
    <?php
    unset($_SESSION['warning_message']); // nettoie le message après affichage
    ?>
<?php endif; ?>

<main>
    <form action="frontcontroller.php?page=contact" method="POST">
        <div>
            <label for="civilite">Civilité :</label>
            <select id="civilite" name="civilite">
                <option value="mme">Mme</option>
                <option value="mr">Mr</option>
            </select>
        </div>

        <div>
            <label for="nom">Nom</label>
            <input id="nom" type="text" name="user_nom">
        </div>

        <div>
            <label for="prenom">Prénom</label>
            <input id="prenom" type="text" name="user_prenom">
        </div>

        <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="user_email">
        </div>

        <fieldset>
            <legend>Raison du contact : </legend>
            <div>
                <input id="comptabilite" type="radio" name="raison_contact">
                <label for="comptabilite">Comptabilité</label>
            </div>
            <div>
                <input id="informatique" type="radio" name="raison_contact">
                <label for="informatique">Informatique</label>
            </div>
        </fieldset>

        <div>
            <label for="message">Message</label>
            <textarea id="message" name="user_message"></textarea>
        </div>

        <div>
            <button type="submit">Envoyer</button>
        </div>
    </form>
</main>

    <!-- On nettoie les données du formulaire après affichage -->
<?php
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>