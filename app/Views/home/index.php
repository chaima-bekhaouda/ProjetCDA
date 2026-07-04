<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest</title>
</head>
<body>
    <h1>BookNest</h1>

    <?php if (empty($books)): ?>
        <p>Aucun livre pour le moment.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($books as $book): ?>
                <li>
                    <?= htmlspecialchars($book['title']) ?> — <?= htmlspecialchars($book['author']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>