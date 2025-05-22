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
$fichier_info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Vérification que le formulaire a bien été soumis via POST

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

    // trim retire les espaces inutiles
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

    // Validation et traitement du fichier uploadé
    $nom_fichier_final = '';
    if (isset($_FILES['fichier_joint']) && $_FILES['fichier_joint']['error'] !== UPLOAD_ERR_NO_FILE) {
        $fichier = $_FILES['fichier_joint'];

        //Vérification des erreurs d'upload
        if ($fichier['error'] !== UPLOAD_ERR_OK) {
            switch ($fichier['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $erreurs['fichier'] = "Le fichier est trop volumineux.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $erreurs['fichier'] = "Le fichier n'a été que partiellement téléchargé.";
                    break;
                default:
                    $erreurs['fichier'] = "Erreur lors du téléchargement du fichier.";
            }
        } else {
            // Vérification de la taille (5 MB)
            $taille_max = 5 * 1024 * 1024;
            if ($fichier['size'] > $taille_max) {
                $erreurs['fichier'] = "Le fichier ne peut pas dépasser 5 MB.";
            }

            // Vérification du type de fichier
            $extensions_autorisees = ['jpg', 'jpeg', 'gif', 'png', 'pdf', 'doc', 'docx', 'txt', 'zip'];
            $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $extensions_autorisees)) {
                $erreurs['fichier'] = "Type de fichier non autorisé. Extensions acceptées : " . implode(', ', $extensions_autorisees)   ;
            }

            // Si pas d'erreur, on prépare le fichier pour sauvegarde
            if (!isset($erreurs['fichier'])) {
                // Création d'un nom unique pour éviter les conflits
                $nom_fichier_original = pathinfo($fichier['name'], PATHINFO_FILENAME);
                $nom_fichier_unique = $nom_fichier_original . ' ' . uniqid() . '.' . $extension;

                // Création du dossier storage s'il n'existe pas
                $dossier_storage = "storage";
                if (!is_dir($dossier_storage)) {
                    mkdir($dossier_storage, 0777, true);
                }

                $chemin_destination = $dossier_storage . '/' . $nom_fichier_unique;

                // Déplacement du fichier vers le dossier storage
                if (move_uploaded_file($fichier['tmp_name'], $chemin_destination)) {
                    $nom_fichier_final = $nom_fichier_unique;
                    $fichier_info = "Fichier original : " . $fichier['name'] . " | Taille : " . round($fichier['size'] / 1024, 2) . "KB";
                } else {
                    $erreurs['fichier'] = "Erreur lors de la sauvegarde du fichier.";
                }
            }
        }
    }


    // Traitement du formulaire si aucune erreur
    if (empty($erreurs)) {
        // Création d'une chaîne formatée avec les données du formulaire
        $donnees_formulaire = "--- Nouvelle soumission de formulaire ---\n";
        $donnees_formulaire .= "Date : " . date("Y-m-d H:i:s") . "\n";
        $donnees_formulaire .= "Civilité : " . $civilite . "\n";
        $donnees_formulaire .= "Nom : " . $nom . "\n";
        $donnees_formulaire .= "Prénom : " . $prenom . "\n";
        $donnees_formulaire .= "Email : " . $email . "\n";
        $donnees_formulaire .= "Raison du contact : " . $raison . "\n";
        $donnees_formulaire .= "Message : " . $message . "\n";
        $donnees_formulaire .= "Fichier joint : " . $nom_fichier_final . "\n";
        $donnees_formulaire .= $fichier_info . "\n";
        $donnees_formulaire .= "------------------------------------\n";

        $fichier = "contacts/formulaires_contact.txt"; // chemin du fichier
        $repertoire = dirname($fichier);
        if (!is_dir($repertoire)) {  // création du dossier s'il n'existe pas
            mkdir($repertoire, 0755, true); // création récursive avec permissions
        }

        // Enregistrement des données grâce aux drapeaux file_append (ajout sans écraser) et lock (empêche d'écrire dans le fichier)
        $resultat = file_put_contents($fichier, $donnees_formulaire, FILE_APPEND | LOCK_EX);

        // Vérification que l'enregistrement a fonctionné
        if ($resultat === false) {
            $_SESSION['warning_message'] = "Votre message n'a pas été enregistré. Nous revenons vers vous très vite.";
        } else {
            $_SESSION['success_message'] = "Votre message a bien été enregistré.";
        }

        // Redirection vers la page de confirmation
        header('Location: frontcontroller.php?page=contact-confirmation');
        exit; // ajout obligatoire après header
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
            'fichier_info' => $fichier_info,
        ];
    }
}
?>

<!-- Affichage des erreurs s'il y en a -->
<?php if (!empty($_SESSION['form_errors'])) : ?>
    <div>
        <ul>
            <?php foreach ($_SESSION['form_errors'] as $erreur) : ?>
                <li><?php echo htmlspecialchars($erreur); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['form_errors']); ?> <!--nettoie les erreurs après affichage-->
<?php endif; ?>

<!-- Affichage du message d'avertissement -->
<?php if (isset($_SESSION['warning_message'])): ?>
    <div>
        <?php echo htmlspecialchars($_SESSION['warning_message']); ?>
    </div>
    <?php unset($_SESSION['warning_message']); ?> <!--nettoie le message après affichage-->
<?php endif; ?>

<!-- Affichage du message de succès -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div>
        <?php echo htmlspecialchars($_SESSION['success_message']); ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<main>
    <h1>Formulaire de contact</h1>

    <form action="frontcontroller.php?page=contact" method="POST" enctype="multipart/form-data">

        <div>
            <?php if (isset($erreurs['civilite'])): ?>
                <div><?php echo htmlspecialchars($erreurs['civilite']); ?></div>
            <?php endif; ?>
            <label for="civilite">Civilité :</label>
            <select id="civilite" name="civilite">
                <option value="">-- Sélectionner --</option>
                <option value="mme">Mme</option>
                <option value="mr">Mr</option>
            </select>
        </div>

        <div>
            <?php if (isset($erreurs['nom'])): ?>
                <div><?php echo htmlspecialchars($erreurs['nom']); ?></div>
            <?php endif; ?>
            <label for="nom">Nom :</label>
            <input id="nom" type="text" name="user_nom">
        </div>

        <div>
            <?php if (isset($erreurs['prenom'])): ?>
                <div><?php echo htmlspecialchars($erreurs['prenom']); ?></div>
            <?php endif; ?>
            <label for="prenom">Prénom :</label>
            <input id="prenom" type="text" name="user_prenom">
        </div>

        <div>
            <?php if (isset($erreurs['email'])): ?>
                <div><?php echo htmlspecialchars($erreurs['email']); ?></div>
            <?php endif; ?>
            <label for="email">Email :</label>
            <input id="email" type="email" name="user_email">
        </div>

        <fieldset>
            <legend>Raison du contact :</legend>
            <?php if (isset($erreurs['raison_contact'])): ?>
                <div><?php echo htmlspecialchars($erreurs['raison_contact']); ?></div>
            <?php endif; ?>

            <div>
                <input id="comptabilite" type="radio" name="raison_contact" value="comptabilite">
                <label for="comptabilite">Comptabilité</label>
            </div>

            <div>
                <input id="informatique" type="radio" name="raison_contact" value="informatique">
                <label for="informatique">Informatique</label>
            </div>
        </fieldset>

        <div>
            <?php if(isset($erreurs['message'])): ?>
                <div><?php echo htmlspecialchars($erreurs['message']); ?></div>
            <?php endif; ?>
            <label for="message">Message :</label>
            <textarea id="message" name="user_message" placeholder="Votre message (minimum 5 caractères)"></textarea>
        </div>

        <div>
            <?php if(isset($erreurs['fichier'])): ?>
                <div><?php echo htmlspecialchars($erreurs['fichier']); ?></div>
            <?php endif; ?>
            <label for="fichier">Fichier :</label>
            <input id="fichier" type="file" name="fichier_joint">
        </div>

        <div>
            <button type="submit">Envoyer</button>
        </div>
    </form>
</main>

<?php // nettoie les données du formulaire après affichage
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>