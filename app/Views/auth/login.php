<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - BookNest</title>
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
                    <h1>Retrouvez vos livres, vos étagères, votre refuge.</h1>
                    <p>
                        Entrez dans votre bibliothèque personnelle et reprenez le fil de vos lectures
                        dans une atmosphère feutrée, pensée comme une vraie pièce remplie de livres.
                    </p>
                </div>
            </div>

            <div class="shelf-zone">
                <div class="books">
                    <div class="book t1"></div>
                    <div class="book t2"></div>
                    <div class="book t3"></div>
                    <div class="book t4"></div>
                    <div class="book t5"></div>
                    <div class="book t6"></div>
                </div>
                <div class="shelf"></div>

                <p class="quote">“Chaque livre attend son heure, chaque lecteur son refuge.”</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="form-card">
                <h2>Connexion</h2>
                <p class="intro">
                    Connectez-vous pour retrouver votre collection, poursuivre vos lectures
                    et parcourir vos rayons.
                </p>

                <?php if (!empty($error)): ?>
                    <div class="alert"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="/login">
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" required>
                    </div>

                    <div class="field">
                        <label for="password">Mot de passe</label>
                        <input id="password" type="password" name="password" required>
                    </div>

                    <button class="submit-btn" type="submit">Se connecter</button>
                </form>

                <p class="form-footer">
                    Vous n’avez pas encore de compte ?
                    <a href="/register">Créer un compte</a>
                </p>
            </div>
        </section>
    </div>
</body>
</html>