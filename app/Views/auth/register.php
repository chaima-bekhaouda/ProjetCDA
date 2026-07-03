<div class="auth-card">
    <h1>Créer un compte</h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="/register">
        <label>Nom</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Mot de passe</label>
        <input type="password" name="password" required>

        <button type="submit">Créer le compte</button>
    </form>

    <p><a href="/login">Déjà un compte ? Connexion</a></p>
</div>