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
<?php
$books = $books ?? [];
$stats = $stats ?? [];
$search = $search ?? '';
$selectedGenre = $selectedGenre ?? '';
$selectedStatus = $selectedStatus ?? '';
$readingBooks = array_values(array_filter(
    $books,
    fn(array $book): bool => ($book['status'] ?? '') === 'reading'
));

$otherBooks = array_values(array_filter(
    $books,
    fn(array $book): bool => ($book['status'] ?? '') !== 'reading'
));

$splitIndex = (int) ceil(count($otherBooks) / 2);

$shelfTwoBooks = array_slice($otherBooks, 0, $splitIndex);
$shelfThreeBooks = array_slice($otherBooks, $splitIndex);


$coverMap = [
    "La Maison d'à côté" => "/assets/images/books/la-maison-da-cote.jpeg",
    "Les Morsures du passé" => "/assets/images/books/les-morsures-du-passe.jpeg",
    "Preuves d'amour" => "/assets/images/books/preuves-damour.jpeg",
    "Arrêtez-moi" => "/assets/images/books/arretez-moi.jpeg",
    "Famille parfaite" => "/assets/images/books/famille-parfaite.jpeg",
    "Lumière noire" => "/assets/images/books/lumiere-noire.jpeg",
];

$statusLabelMap = [
    'to_read' => 'À lire',
    'reading' => 'En cours',
    'finished' => 'Terminé',
];

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function bookCoverPath(array $book, array $coverMap): string
{
    $title = $book['title'] ?? '';
    return $coverMap[$title] ?? '/assets/images/books/la-maison-da-cote.jpeg';
}

function bookStatusLabel(string $status, array $statusLabelMap): string
{
    return $statusLabelMap[$status] ?? $status;
}

function bookToneClass(int $index): string
{
    $tones = ['tone-1', 'tone-2', 'tone-3', 'tone-4', 'tone-5', 'tone-6'];
    return $tones[$index % count($tones)];
}
?>
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
            <form class="home-search" action="/" method="get">
                <input
    type="text"
    name="q"
    class="home-search-input"
    placeholder="Chercher un titre, un auteur..."
    value="<?= e($search ?? '') ?>"
>
            </form>
        </div>
        

        <div class="header-right">
            <?php if (!empty($_SESSION['user']['display_name'])): ?>
    <div class="profile-menu">
        <button type="button" class="profile-toggle" id="profile-toggle">
            <span class="profile-avatar">
                <?= strtoupper(substr(e($_SESSION['user']['display_name']), 0, 1)) ?>
            </span>
            <span class="profile-name"><?= e($_SESSION['user']['display_name']) ?></span>
            <span class="profile-chevron">▾</span>
        </button>

        <div class="profile-dropdown" id="profile-dropdown">
            <div class="profile-dropdown-head">
                <strong><?= e($_SESSION['user']['display_name']) ?></strong>
                <span>Connecté sur BookNest</span>
            </div>

            <form method="post" action="/logout" class="logout-form">
                <button type="submit" class="logout-btn">Se déconnecter</button>
            </form>
        </div>
    </div>
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
                    Cliquez sur un livre pour ouvrir la fiche et le supprimer si besoin.
                </p>
            </div>

            <div class="hero-right">
                <div class="stat-card">
                    <span class="stat-number"><?= (int) ($stats['total_books'] ?? 0) ?></span>
                    <span class="stat-label">LIVRES</span>
                </div>

                <div class="stat-card">
                    <span class="stat-number"><?= (int) ($stats['finished_books'] ?? 0) ?></span>
                    <span class="stat-label">TERMINÉS</span>
                </div>

                <div class="stat-card">
                    <span class="stat-number"><?= (int) ($stats['reading_books'] ?? 0) ?></span>
                    <span class="stat-label">EN COURS</span>
                </div>

                <div class="stat-card">
                    <span class="stat-number"><?= (int) ($stats['total_pages'] ?? 0) ?></span>
                    <span class="stat-label">PAGES LUES</span>
                </div>
            </div>
        </section>
    

        <section class="filters-section">
    <div class="filters-col">
    <details class="filter-dropdown">
        <summary class="filter-chip filter-chip--dropdown">
            Genre<?= $selectedGenre !== '' ? ' : ' . e($selectedGenre) : '' ?>
        </summary>

        <div class="filter-dropdown-menu">
            <a href="/?q=<?= urlencode($search) ?>&genre=&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === '' ? 'active' : '' ?>">Tous les genres</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Roman&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Roman' ? 'active' : '' ?>">Roman</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Classique&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Classique' ? 'active' : '' ?>">Classique</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Thriller&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Thriller' ? 'active' : '' ?>">Thriller</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Policier&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Policier' ? 'active' : '' ?>">Policier</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Science-fiction&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Science-fiction' ? 'active' : '' ?>">Science-fiction</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Fantasy&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Fantasy' ? 'active' : '' ?>">Fantasy</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Fantastique&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Fantastique' ? 'active' : '' ?>">Fantastique</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Horreur&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Horreur' ? 'active' : '' ?>">Horreur</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Romance&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Romance' ? 'active' : '' ?>">Romance</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Historique&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Historique' ? 'active' : '' ?>">Historique</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Essai&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Essai' ? 'active' : '' ?>">Essai</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Biographie&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Biographie' ? 'active' : '' ?>">Biographie</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Autobiographie&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Autobiographie' ? 'active' : '' ?>">Autobiographie</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Poésie&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Poésie' ? 'active' : '' ?>">Poésie</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Théâtre&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Théâtre' ? 'active' : '' ?>">Théâtre</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=BD&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'BD' ? 'active' : '' ?>">BD</a>
            <a href="/?q=<?= urlencode($search) ?>&genre=Manga&status=<?= urlencode($selectedStatus) ?>" class="filter-dropdown-item <?= $selectedGenre === 'Manga' ? 'active' : '' ?>">Manga</a>
        </div>
    </details>
</div>

    <div class="filters-col">
        <a href="/?q=<?= urlencode($search) ?>&genre=<?= urlencode($selectedGenre) ?>&status=" class="filter-chip <?= $selectedStatus === '' ? 'active' : '' ?>"> Tous statuts</a>
        <a href="/?q=<?= urlencode($search) ?>&genre=<?= urlencode($selectedGenre) ?>&status=to_read"class="filter-chip <?= $selectedStatus === 'to_read' ? 'active' : '' ?>">À lire</a>
        <a href="/?q=<?= urlencode($search) ?>&genre=<?= urlencode($selectedGenre) ?>&status=reading"class="filter-chip <?= $selectedStatus === 'reading' ? 'active' : '' ?>">En cours</a>
        <a href="/?q=<?= urlencode($search) ?>&genre=<?= urlencode($selectedGenre) ?>&status=finished"class="filter-chip <?= $selectedStatus === 'finished' ? 'active' : '' ?>">Terminé</a>
        <a href="#"class="filter-chip"onclick="return false;">Prêté</a>
    </div>
</section>
<?php if (!empty($search)): ?>
    <div class="search-back-wrap">
        <a href="/" class="search-back-btn">Retour aux étagères</a>
    </div>
<?php endif; ?>
        <section class="library-section">
            <div class="shelf-block">
                <div class="shelf-row shelf-row--covers">
                    <?php foreach ($readingBooks as $index => $book): ?>
                        <?php
                        $bookId = e($book['id']);
                        $title = e($book['title']);
                        $author = e($book['author']);
                        $year = e($book['year']);
                        $genre = e($book['genre']);
                        $pages = e($book['pages']);
                        $statusRaw = (string) ($book['status'] ?? '');
                        $statusLabel = e(bookStatusLabel($statusRaw, $statusLabelMap));
                        $quote = e($book['notes'] ?? '');
                        $cover = e(bookCoverPath($book, $coverMap));
                        ?>
                        <button
                            type="button"
                            class="cover-book trigger-book-modal"
                            data-id="<?= $bookId ?>"
                            data-title="<?= $title ?>"
                            data-author="<?= $author ?>"
                            data-year="<?= $year ?>"
                            data-genre="<?= $genre ?>"
                            data-pages="<?= $pages ?>"
                            data-status="<?= $statusLabel ?>"
                            data-quote="<?= $quote ?>"
                            data-cover="<?= $cover ?>"
                        >
                            <img src="<?= $cover ?>" alt="<?= $title ?>">
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="shelf-plank"></div>

                <div class="shelf-row shelf-row--spines">
                    <?php foreach ($shelfTwoBooks as $index => $book): ?>
                        <?php
                        $bookId = e($book['id']);
                        $title = e($book['title']);
                        $author = e($book['author']);
                        $year = e($book['year']);
                        $genre = e($book['genre']);
                        $pages = e($book['pages']);
                        $statusRaw = (string) ($book['status'] ?? '');
                        $statusLabel = e(bookStatusLabel($statusRaw, $statusLabelMap));
                        $quote = e($book['notes'] ?? '');
                        $cover = e(bookCoverPath($book, $coverMap));
                        $coverColor = e($book['cover_color'] ?? '#5c3b2e');
                        $tone = bookToneClass($index);
                        ?>
                        <button
                            type="button"
                            class="spine-book trigger-book-modal <?= $tone ?>"
                            data-id="<?= $bookId ?>"
                            data-title="<?= $title ?>"
                            data-author="<?= $author ?>"
                            data-year="<?= $year ?>"
                            data-genre="<?= $genre ?>"
                            data-pages="<?= $pages ?>"
                            data-status="<?= $statusLabel ?>"
                            data-quote="<?= $quote ?>"
                            data-cover="<?= $cover ?>"
                            style="background: linear-gradient(180deg, <?= $coverColor ?> 0%, #241109 100%);"
                        >
                            <span><?= $title ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="shelf-plank"></div>

                <div class="shelf-row shelf-row--decor">
                    <div class="decor-book decor-book--plant">
                        <img src="/assets/images/books/plant.png" alt="" aria-hidden="true">
                    </div>

                    <?php foreach ($shelfThreeBooks as $index => $book): ?>
                        <?php
                        $bookId = e($book['id']);
                        $title = e($book['title']);
                        $author = e($book['author']);
                        $year = e($book['year']);
                        $genre = e($book['genre']);
                        $pages = e($book['pages']);
                        $statusRaw = (string) ($book['status'] ?? '');
                        $statusLabel = e(bookStatusLabel($statusRaw, $statusLabelMap));
                        $quote = e($book['notes'] ?? '');
                        $cover = e(bookCoverPath($book, $coverMap));
                        $coverColor = e($book['cover_color'] ?? '#5c3b2e');
                        $tone = bookToneClass($index + 1);
                        $tallClass = $index % 2 === 0 ? 'tall' : '';
                        ?>
                        <button
                            type="button"
                            class="spine-book trigger-book-modal <?= $tallClass ?> <?= $tone ?>"
                            data-id="<?= $bookId ?>"
                            data-title="<?= $title ?>"
                            data-author="<?= $author ?>"
                            data-year="<?= $year ?>"
                            data-genre="<?= $genre ?>"
                            data-pages="<?= $pages ?>"
                            data-status="<?= $statusLabel ?>"
                            data-quote="<?= $quote ?>"
                            data-cover="<?= $cover ?>"
                            style="background: linear-gradient(180deg, <?= $coverColor ?> 0%, #241109 100%);"
                        >
                            <span><?= $title ?></span>
                        </button>
                    <?php endforeach; ?>

                    <div class="decor-book decor-book--globe">
                        <img src="/assets/images/books/globe.png" alt="" aria-hidden="true">
                    </div>
                </div>

                <div class="shelf-plank"></div>
            </div>
        </section>
    
    </main>
</div>

<dialog id="book-modal" class="book-modal">
    <div class="book-modal__panel">
        <button type="button" class="book-modal__close" id="book-modal-close" aria-label="Fermer">×</button>

        <div class="book-modal__layout">
            <div class="book-modal__cover-wrap">
                <img id="book-modal-cover" class="book-modal__cover" src="" alt="">
            </div>

            <div class="book-modal__content">
                <span id="book-modal-status" class="book-modal__status"></span>

                <h3 id="book-modal-title" class="book-modal__title"></h3>
                <p id="book-modal-meta" class="book-modal__meta"></p>

                <div class="book-modal__infos">
                    <div class="book-info-card">
                        <span class="book-info-label">Genre</span>
                        <span id="book-modal-genre" class="book-info-value"></span>
                    </div>

                    <div class="book-info-card">
                        <span class="book-info-label">Pages</span>
                        <span id="book-modal-pages" class="book-info-value"></span>
                    </div>
                </div>

                <blockquote id="book-modal-quote" class="book-modal__quote"></blockquote>

                <div class="book-modal__actions">
                    <form action="/books/delete" method="post" class="delete-book-form">
                        <input type="hidden" name="book_id" id="book-modal-id" value="">
                        <button type="submit" class="modal-action modal-action--danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</dialog>

<script>
const bookModal = document.getElementById('book-modal');
const modalCloseBtn = document.getElementById('book-modal-close');

const modalBookId = document.getElementById('book-modal-id');
const modalCover = document.getElementById('book-modal-cover');
const modalStatus = document.getElementById('book-modal-status');
const modalTitle = document.getElementById('book-modal-title');
const modalMeta = document.getElementById('book-modal-meta');
const modalGenre = document.getElementById('book-modal-genre');
const modalPages = document.getElementById('book-modal-pages');
const modalQuote = document.getElementById('book-modal-quote');

document.querySelectorAll('.trigger-book-modal').forEach((button) => {
    button.addEventListener('click', () => {
        const id = button.dataset.id || '';
        const title = button.dataset.title || '';
        const author = button.dataset.author || '';
        const year = button.dataset.year || '';
        const genre = button.dataset.genre || '';
        const pages = button.dataset.pages || '';
        const status = button.dataset.status || '';
        const quote = button.dataset.quote || '';
        const cover = button.dataset.cover || '';

        if (modalBookId) {
            modalBookId.value = id;
        }

        modalCover.src = cover;
        modalCover.alt = title;
        modalStatus.textContent = status;
        modalTitle.textContent = title;
        modalMeta.textContent = `${author} · ${year}`;
        modalGenre.textContent = genre;
        modalPages.textContent = pages;
        modalQuote.textContent = quote ? `« ${quote} »` : '';

        if (bookModal) {
            bookModal.showModal();
        }
    });
});

if (modalCloseBtn) {
    modalCloseBtn.addEventListener('click', () => {
        bookModal.close();
    });
}

if (bookModal) {
    bookModal.addEventListener('click', (event) => {
        const panel = bookModal.querySelector('.book-modal__panel');
        if (panel && !panel.contains(event.target)) {
            bookModal.close();
        }
    });
}
const profileToggle = document.getElementById('profile-toggle');
const profileMenu = document.querySelector('.profile-menu');

if (profileToggle && profileMenu) {
    profileToggle.addEventListener('click', (event) => {
        event.stopPropagation();
        profileMenu.classList.toggle('is-open');
    });

    document.addEventListener('click', (event) => {
        if (!profileMenu.contains(event.target)) {
            profileMenu.classList.remove('is-open');
        }
    });
}
</script>
</body>
</html>