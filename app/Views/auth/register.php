<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - BookNest</title>
</head>
<body>
    <h1>Inscription</h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="/register">
        <label>Nom affiché</label>
        <input type="text" name="display_name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Mot de passe</label>
        <input type="password" name="password" required>

        <button type="submit">Créer le compte</button>
    </form>

    <p><a href="/login">J’ai déjà un compte</a></p>
</body>
</html>