<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/home.css">
</head>
<body>
    <div class="home-shell">
        <header class="home-header">
    <div class="header-left">
        <div class="brand">
            <img
                src="/assets/images/logobooknest.png"
                alt="Logo BookNest"
                class="brand-logo"
            >
            <div class="brand-text">
                <h1>BookNest</h1>
                <p>Votre collection personnelle</p>
            </div>
        </div>
    </div>

    <div class="header-center">
        <form class="home-search" action="/search" method="get">
            <input
                type="text"
                name="q"
                class="home-search-input"
                placeholder="Chercher un titre, un auteur..."
            >
        </form>
    </div>

    <div class="header-right">
        <?php if (!empty($_SESSION['user']['display_name'])): ?>
            <span class="user-name"><?= htmlspecialchars($_SESSION['user']['display_name']) ?></span>
        <?php else: ?>
            <a class="action-btn login-btn" href="/login">Connexion</a>
        <?php endif; ?>

        <a class="action-btn add-btn" href="/books/create">+ Ajouter</a>
    </div>
</header>
        <main class="home-main">
            <section class="hero-section">
    <div class="hero-left">
        <span class="hero-eyebrow">RAYON PRINCIPAL</span>
        <h2>
            Chaque livre a sa place.<br>
            <em>Chaque histoire, son étagère.</em>
        </h2>
        <p>
            Parcourez votre collection comme une vraie bibliothèque.
            Cliquez sur une tranche pour ouvrir le livre, suivez vos lectures et vos prêts.
        </p>
    </div>

    <div class="hero-right">
        <div class="stat-card">
            <span class="stat-number">27</span>
            <span class="stat-label">LIVRES</span>
        </div>

        <div class="stat-card">
            <span class="stat-number">11</span>
            <span class="stat-label">TERMINÉS</span>
        </div>

        <div class="stat-card">
            <span class="stat-number">4</span>
            <span class="stat-label">EN COURS</span>
        </div>

        <div class="stat-card">
            <span class="stat-number">3769</span>
            <span class="stat-label">PAGES LUES</span>
        </div>
    </div>
</section>

<section class="filters-section">
    <div class="filters-col">
        <button class="filter-chip active" type="button">Tous</button>
        <button class="filter-chip" type="button">Roman</button>
        <button class="filter-chip" type="button">Classique</button>
        <button class="filter-chip" type="button">SF</button>
        <button class="filter-chip" type="button">Essai</button>
    </div>

    <div class="filters-col">
        <button class="filter-chip active" type="button">Tous statuts</button>
        <button class="filter-chip" type="button">À lire</button>
        <button class="filter-chip" type="button">En cours</button>
        <button class="filter-chip" type="button">Terminé</button>
        <button class="filter-chip" type="button">Prêté</button>
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