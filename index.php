<html>
    <head>
        <title>Welcome to MMI !</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Obviously&family=Chill+Script&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    </head>
    <body>
        <div class="flex">
            <img src="assets/ge/peace.svg" class="traversing-image">
            <header>
                <img src="assets/ge/interogation.svg" alt="" class="large-icon index">
                <h1 class="welcome-text">Welcome !</h1>
                <p>Découvre <span>ton parrain mystère</span> et commence ton aventure à l'IUT ! ✨</p>
                <h2>🌟 🎉 🚀</h2>
            </header>
            <main>
                <div>
                    <form action="scripts/checkWelcomeCode.php" method="POST">
                        <img src="assets/ge/logo.png" alt="" class="logo-fixed">
                        <label for="welcomeCode">Entre ton code secret 🔐</label>
                        <input type="text" maxlength="" placeholder="EFDHTGF4" name="welcomeCode">
                        <button>Commencer l'aventure ! 🚀</button>
                    </form>
                    <div>
                        <p>Ton code te donne accès
                        à un chat anonyme avec ton parrain/marraine</p>
                        <p class="welcomeCodeLosted"><a href="">J'ai perdu mon code d'accès 😔</a></p>
                    </div>
                </div>
            </main>
        </div>
        <footer>
            <p>Développé par Théo Manya pour le <span> BDE MMI <img src="assets/ge/logo.png" alt=""></span></p>
        </footer>
    </body>
</html>