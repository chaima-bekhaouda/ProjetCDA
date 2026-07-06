<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - BookNest</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-shell">
        <section class="auth-visual">
            <div>
                <div class="brand">
                    <div class="brand-logo">
                        <img src="/assets/images/logobooknest.png" alt="Logo BookNest">
                    </div>
                    <div class="brand-text">
                        <strong>BookNest</strong>
                    </div>
                </div>

                <div class="hero-copy">
                    <h1>Commencez votre bibliothèque, un livre à la fois.</h1>
                    <p>
                        Créez votre espace BookNest pour rassembler vos lectures, organiser vos rayons
                        et faire naître une bibliothèque qui vous ressemble.
                    </p>
                </div>
            </div>

            <div class="shelf-zone">
                <div class="books">
                    <div class="book t3"></div>
                    <div class="book t6"></div>
                    <div class="book t2"></div>
                    <div class="book t5"></div>
                    <div class="book t1"></div>
                    <div class="book t4"></div>
                </div>
                <div class="shelf"></div>

                <p class="quote">“Ouvrez la porte, les livres vous reconnaîtront.”</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="form-card">
                <h2>Inscription</h2>
                <p class="intro">
                    Créez votre compte pour commencer à construire votre bibliothèque personnelle.
                </p>

                <?php if (!empty($error)): ?>
                    <div class="alert"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="/register">
                    <div class="field">
                        <label for="display_name">Nom affiché</label>
                        <input id="display_name" type="text" name="display_name" required>
                    </div>

                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" required>
                    </div>

                    <div class="field">
                        <label for="password">Mot de passe</label>
                        <input id="password" type="password" name="password" required>
                    </div>

                    <button class="submit-btn" type="submit">Créer le compte</button>
                </form>

                <p class="form-footer">
                    Vous avez déjà un compte ?
                    <a href="/login">Se connecter</a>
                </p>
            </div>
        </section>
    </div>
</body>
</html>