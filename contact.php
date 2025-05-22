<?php
$pageTitle = "Contact";
$metaDescription = "Formulaire de contact";

// Initialisation du tableau d'erreurs
$erreurs = [];

// Vérification que le formulaire a bien été soumis via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
        // redirection avec message de succès
        $_SESSION['success_message'] = "Votre message a bien été envoyé";
        header('Location: frontcontroller.php?page=contact_confirmation');
    } else {
        $_SESSSION['form_errors'] = $erreurs; // en cas d'erreur, on les stocke en session pour les afficher sur le formulaire
        // on conserve les données valides saisies pour éviter à l'utilisateur de tout remplir à nouveau
        $_SESSSION['form_data'] = [
            'civilite' => isset($civilite) ? $civilite : '',
            'nom' => isset($nom) ? $nom : '',
            'prenom' => isset($prenom) ? $prenom : '',
            'email' => isset($email) ? $email : '',
            'raison_contact' => isset($raison) ? $raison : '',
            'message' => isset($message) ? $message : '',
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
    // On nettoie les erreurs après affichage
    unset($_SESSION['form_errors']);
    ?>
<?php endif; ?>

<!-- Affichage du message de succès -->
<?php if(isset($_SESSION['success_message'])): ?>
    <div>
        <?php echo $_SESSION['success_message']; ?>
    </div>
    <?php
    // On nettoie le message après affichage
    unset($_SESSION['success_message']);
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

<?php
// On nettoie les données du formulaire après affichage
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>