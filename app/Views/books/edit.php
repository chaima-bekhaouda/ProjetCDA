<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Modifier un livre</title></head>
<body>
<h1>Modifier un livre</h1>

<?php if ($book): ?>
<form method="post" action="/books/update">
    <input type="hidden" name="id" value="<?= $book['id'] ?>">
    <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>">
    <button type="submit">Mettre à jour</button>
</form>
<?php else: ?>
<p>Livre introuvable.</p>
<?php endif; ?>

<a href="/books">Retour</a>
</body>
</html>