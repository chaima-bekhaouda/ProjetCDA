<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest</title>
    <link rel="stylesheet" href="/assets/css/home.css">
</head>
<body>
    <div class="home-shell">
        <header class="home-header">
            <div class="brand">
    <img
        src="/assets/images/logobooknest.png"
        alt="Logo BookNest"
        class="brand-logo"
        />
    <div>
        <h1>BookNest</h1>
        <p>Votre bibliothèque personnelle</p>
    </div>
</div>

            <div class="header-actions">
                <?php if (!empty($_SESSION['user']['display_name'])): ?>
                    <span class="welcome">Bonjour, <?= htmlspecialchars($_SESSION['user']['display_name']) ?></span>
                <?php endif; ?>

                <a class="dashboard-link" href="/dashboard">Dashboard</a>
            </div>
        </header>

        <main class="home-main">
            <section class="hero-card">
                <div class="hero-text">
                    <span class="eyebrow">Ma collection</span>
                    <h2>Retrouve tes livres dans une ambiance bibliothèque.</h2>
                    <p>
                        Consulte rapidement ta sélection, retrouve tes auteurs favoris
                        et garde une vue simple sur ta bibliothèque BookNest.
                    </p>
                </div>

                <div class="hero-stats">
                    <div class="stat-card">
                        <span class="stat-number"><?= count($books) ?></span>
                        <span class="stat-label">Livres affichés</span>
                    </div>
                </div>
            </section>

            <section class="library-section">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Étagère</span>
                        <h3>Mes livres</h3>
                    </div>
                </div>

                <?php if (empty($books)): ?>
                    <div class="empty-state">
                        <p>Aucun livre pour le moment.</p>
                    </div>
                <?php else: ?>
                    <div class="books-grid">
                        <?php foreach ($books as $book): ?>
                            <article class="book-card">
                                <div
                                    class="book-cover"
                                    style="background: <?= htmlspecialchars($book['cover_color'] ?? '#7a2e2a') ?>;"
                                >
                                    <span class="book-cover-title">
                                        <?= htmlspecialchars($book['title']) ?>
                                    </span>
                                </div>

                                <div class="book-body">
                                    <h4><?= htmlspecialchars($book['title']) ?></h4>
                                    <p class="author"><?= htmlspecialchars($book['author']) ?></p>

                                    <div class="meta">
                                        <?php if (!empty($book['genre'])): ?>
                                            <span><?= htmlspecialchars($book['genre']) ?></span>
                                        <?php endif; ?>

                                        <?php if (!empty($book['year'])): ?>
                                            <span><?= htmlspecialchars((string) $book['year']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>