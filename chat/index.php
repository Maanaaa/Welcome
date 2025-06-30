<?php
include("../scripts/includes.php");
include("../scripts/session.php");

if (!isset($_SESSION['welcomeCode'])) {
    header("Location: ../index.php");
    exit();
}

$filleulPrenom = null;
$filleulNom = null;

$requete = 'SELECT filleul_id FROM relations WHERE parrain_id = :parrainId';
$stmt = $connection->prepare($requete);
$stmt->bindParam(':parrainId', $_SESSION['id'], PDO::PARAM_INT);
$stmt->execute();
$filleul = $stmt->fetch(PDO::FETCH_ASSOC);

if ($filleul) {
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
    <link
        href="https://fonts.googleapis.com/css2?family=Obviously&family=Chill+Script&family=Poppins:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon">

</head>

<body>
    <div class="chat-app">
        <header>
            <div>
                <img src="../assets/ge/etoile.svg">
                <div>
                    <?php
                    if (isset($_SESSION['role']) && $_SESSION['role'] == 'parrain' && $filleulPrenom) {
                        echo '<h2>Tu es le parrain de ' . htmlspecialchars($filleulPrenom) . '</h2>';
                    } else {
                        echo '<h2>Ton Parrain Mystère</h2>';
                    }
                    ?>
                    <div>
                        <span></span>
                        En ligne
                    </div>
                </div>
            </div>
            <div><img src="../assets/ge/logo.png"></div>
        </header>

        <main>
            <div>
                🎊 Ton aventure commence ! Dis bonjour à ton parrain mystère ! 🎊
            </div>

            <section>
                <p>Salut ! Bienvenue dans ton aventure la,zdzdoerj ! 🎓</p>
                <time>il y a 2 jours</time>
            </section>

            <section>
                <p>Je suis ton parrain et je suis là pour t'aider ! ✨</p>
                <time>il y a 2 jours</time>
            </section>
        </main>

        <footer>
            <div>
                <button>😊</button>
                <button>📷</button>
                <form id="form">
                    <input type="text" id="message" name="contenu" placeholder="Tape ton message" autocomplete="off" required />
                    <button type="submit">Envoyer</button>
                </form>
            </div>
        </footer>
    </div>
    <img src="../assets/ge/logo-bdemmilepuy.png" alt="" class="logobde-mmi">
    <p style="font-size: 0.6rem;text-align:center;" class="author">Développé par Théo Manya pour le <span> BDE MMI</p>
    <script>
    const currentUserId = <?php echo json_encode($_SESSION['id']); ?>;
    const currentUserRole = <?php echo json_encode($_SESSION['role']); ?>;
    </script>
    <script src="../js/chat.js"></script>

</body>

</html>