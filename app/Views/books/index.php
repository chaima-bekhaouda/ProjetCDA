<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest - Livres</title>
    <style>
        body{margin:0;font-family:Arial,sans-serif;background:#120a08;color:#f5eee8}
        .wrap{max-width:1200px;margin:0 auto;padding:40px 24px}
        h1{font-size:48px;margin:0 0 24px}
        .top{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:24px}
        a.btn,button.btn{display:inline-block;padding:12px 16px;border-radius:12px;background:#d6a98f;color:#1d120f;text-decoration:none;border:none;font-weight:700;cursor:pointer}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px}
        .card{background:#241614;border:1px solid rgba(255,255,255,.08);padding:20px;border-radius:18px;box-shadow:0 10px 24px rgba(0,0,0,.2)}
        .actions{display:flex;gap:8px;margin-top:14px;flex-wrap:wrap}
        .muted{color:#c9b7af}
        input{padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.1);background:#2d1b18;color:#fff;min-width:260px}
    </style>
</head>
<body>
<div class="wrap">
    <h1>Liste des livres</h1>

    <div class="top">
        <a class="btn" href="/books/create">Ajouter un livre</a>
        <a class="btn" href="/dashboard">Dashboard</a>
        <a class="btn" href="/login">Connexion</a>
    </div>

    <div class="grid">
        <?php foreach ($books as $book): ?>
            <div class="card">
                <div><?= htmlspecialchars($book['title']) ?></div>
                <div class="actions">
                    <a class="btn" href="/books/show?id=<?= $book['id'] ?>">Voir</a>
                    <a class="btn" href="/books/edit?id=<?= $book['id'] ?>">Modifier</a>
                    <form method="post" action="/books/delete" style="display:inline">
                        <input type="hidden" name="id" value="<?= $book['id'] ?>">
                        <button class="btn" type="submit">Supprimer</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($books)): ?>
        <p class="muted">Aucun livre pour le moment.</p>
    <?php endif; ?>
</div>
</body>
</html>