<?php
include($_SERVER['DOCUMENT_ROOT'] . '/config/configuration.php');
include($_SERVER['DOCUMENT_ROOT'] . '/config/connection.php');

$code = $_POST["welcomeCode"] ?? '';
// Requête de vérification du code
$requete = 'SELECT id, role, prenom, nom FROM utilisateurs WHERE code = :welcomeCode';
$resultats = $connection->prepare($requete);
$resultats->bindParam(':welcomeCode', $code, PDO::PARAM_STR);
$resultats->execute();

$utilisateur = $resultats->fetch(PDO::FETCH_ASSOC);
$resultats->closeCursor();

if ($utilisateur) {
    // Création du cookie
    // Durée du cookie : 30 jours (en s)
    $cookieLifetime = 30 * 24 * 60 * 60;

    session_set_cookie_params([
        'lifetime' => $cookieLifetime,
        'path' => '/',
        'secure' => true,   
        'httponly' => true, 
        'samesite' => 'Lax' 
    ]);

    session_start();

    $_SESSION['code'] = $code; // Save en session

    // Form caché
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Redirection...</title>
    </head>
    <body>
        <form action="../chat/index.php" method="post" id="hiddenForm">
            <input type="hidden" name="code" value="<?= htmlspecialchars($code, ENT_QUOTES) ?>" />
        </form>

        <script>
          document.getElementById('hiddenForm').submit();
        </script>
    </body>
    </html>
    <?php
    exit;
} else {
    echo "Code utilisateur invalide.";
}