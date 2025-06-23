<?php
include("../scripts/includes.php");

session_start();

if(!isset($_SESSION['welcomeCode'])){
    header("Location: ../index.php");
    exit();
}

$filleulPrenom = null;
$filleulNom = null;

// Requête pour récup l'ID du filleul
$requete = 'SELECT filleul_id FROM relations WHERE parrain_id = :parrainId';
$stmt = $connection->prepare($requete);
$stmt->bindParam(':parrainId', $_SESSION['id'], PDO::PARAM_INT);
$stmt->execute();
$filleul = $stmt->fetch(PDO::FETCH_ASSOC);

if ($filleul) {
    // Requête pour récupérer le prénom du filleul
    $requete = 'SELECT prenom,nom FROM utilisateurs WHERE id = :filleulId';
    $stmt = $connection->prepare($requete);
    $stmt->bindParam(':filleulId', $filleul['filleul_id'], PDO::PARAM_INT);
    $stmt->execute();
    $filleulInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($filleulInfo) {
        $filleulPrenom = $filleulInfo['prenom'];
        $filleulNom = $filleulInfo['nom'];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ton Parrain Mystère</title>
    <link rel="stylesheet" href="../css/chat.css">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Obviously&family=Chill+Script&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="flex">
        <div>
            <header>
                <div>
                    <img src="../assets/ge/enveloppe.svg" alt="Parrain Icon">
                    <div>
                        <?php
                        if (isset($_SESSION['role']) && $_SESSION['role'] == 'parrain') {
                            echo '<h2>Tu es le parrain de '. htmlspecialchars($filleulPrenom) . ' ' . htmlspecialchars($filleulNom). '</h2>';
                        } else {
                            echo '<h2>Ton Parrain Mystère</h2>';
                        }
                        ?>
                        <p>En ligne</p>
                    </div>
                </div>
            </header>
            <main>
                <div id="chat-messages">
                    <div>
                        <p>Dis bonjour à ton parrain mystère !</p>
                    </div>
                    <div>
                        <p>Salut ! Bienvenue...</p>
                        <span>Il y a 2 jours</span>
                    </div>
                    <div>
                        <p>Je suis ton parrain... je suis là pour t'aider !</p>
                        <span>Il y a 2 jours</span>
                    </div>
                </div>
                
                <div id="chat-input">
                    <form>
                        <input type="text" placeholder="Ecrire quelque chose..">
                        <button type="submit"><img src="../assets/ge/enveloppe.svg"></button>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
