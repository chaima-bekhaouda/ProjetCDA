<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest — Livres libre service</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="stylesheet" href="/assets/css/library.css">
</head>
<body class="library-page">
<div class="home-shell">
    <header class="home-header">
        <div class="header-left">
            <div class="brand">
                <img src="/assets/images/logobooknest.png" alt="Logo BookNest" class="brand-logo">
                <div class="brand-text">
                    <h1>BookNest</h1>
                    <p>Livres libre service</p>
                </div>
            </div>
        </div>

        <div class="header-center">
            <form class="home-search" action="/library" method="get">
                <input
                    type="text"
                    name="q"
                    class="home-search-input"
                    placeholder="Chercher un titre, un auteur..."
                    value="<?= htmlspecialchars($query ?? '') ?>"
                >
            </form>
        </div>

        <div class="header-right">
            <a class="action-btn" href="/">← Retour à mes étagères</a>
        </div>
    </header>

    <main class="home-main">
        <section class="library-intro">
            <h2>Liste de livres</h2>
            <p>Des livres du domaine public, en accès libre et gratuit. Ajoutez-les à vos étagères ou lisez-les directement.</p>
            <?php if (!empty($added)): ?>
                <div class="library-banner">✓ Livre ajouté à vos étagères.</div>
            <?php endif; ?>
        </section>

        <section class="library-grid">
            <?php if (empty($books)): ?>
                <p class="library-empty">Aucun livre trouvé. Essayez une autre recherche.</p>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <article class="library-card">
                        <div class="library-card__cover">
                            <?php if (!empty($book['cover_url'])): ?>
                                <img src="<?= htmlspecialchars($book['cover_url']) ?>" alt="Couverture de <?= htmlspecialchars($book['title']) ?>">
                            <?php else: ?>
                                <div class="library-card__cover-placeholder"></div>
                            <?php endif; ?>
                        </div>

                        <div class="library-card__body">
                            <h3 class="library-card__title"><?= htmlspecialchars($book['title']) ?></h3>
                            <p class="library-card__author"><?= htmlspecialchars($book['author']) ?></p>
                            <?php if (!empty($book['page_count'])): ?>
                                <p class="library-card__meta"><?= (int) $book['page_count'] ?> pages</p>
                            <?php endif; ?>

                            <div class="library-card__actions">
                                <form action="/library/add" method="post">
                                    <input type="hidden" name="volume_id" value="<?= htmlspecialchars($book['volume_id']) ?>">
                                    <input type="hidden" name="title" value="<?= htmlspecialchars($book['title']) ?>">
                                    <input type="hidden" name="author" value="<?= htmlspecialchars($book['author']) ?>">
                                    <input type="hidden" name="cover_url" value="<?= htmlspecialchars($book['cover_url'] ?? '') ?>">
                                    <input type="hidden" name="page_count" value="<?= htmlspecialchars((string) ($book['page_count'] ?? '')) ?>">
                                    <input type="hidden" name="categories" value="<?= htmlspecialchars($book['categories'] ?? '') ?>">
                                    <button type="submit" class="library-btn library-btn--primary">+ Ajouter à mes étagères</button>
                                </form>

                                <a href="/library/read?volume_id=<?= urlencode($book['volume_id']) ?>" class="library-btn">Lire</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>