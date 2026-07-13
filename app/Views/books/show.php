<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Détail livre</title></head>
<body>
<h1>Détail du livre</h1>

<?php if ($book): ?>
<p><strong>ID :</strong> <?= $book['id'] ?></p>
<p><strong>Titre :</strong> <?= htmlspecialchars($book['title']) ?></p>
<?php else: ?>
<p>Livre introuvable.</p>
<?php endif; ?>

<a href="/books">Retour</a>
</body>
</html>