<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/home.css?v=<?= @filemtime(__DIR__ . '/../../../public/assets/css/home.css') ?>">
</head>
<body>
<?php
$books = $books ?? [];
$stats = $stats ?? [];
$search = $search ?? '';
$selectedGenre = $selectedGenre ?? '';
$selectedStatus = $selectedStatus ?? '';

$extraShelves = $extraShelves ?? 0;

// Pagination : 6 livres sur la première étagère, puis 12 par étagère.
// On garantit toujours au moins 2 étagères (pour la plante/le globe),
// plus une étagère vide en réserve, plus les étagères ajoutées manuellement.
$shelves = [];
$remaining = $books;
$shelves[] = array_splice($remaining, 0, 6);
while (!empty($remaining)) {
    $shelves[] = array_splice($remaining, 0, 12);
}
while (count($shelves) < 2) {
    $shelves[] = [];
}
$trailingEmpty = 1 + max(0, (int) $extraShelves);
for ($i = 0; $i < $trailingEmpty; $i++) {
    $shelves[] = [];
}

$statusLabelMap = [
    'to_read' => 'À lire',
    'reading' => 'En cours',
    'finished' => 'Terminé',
];

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function bookCoverPath(array $book): string
{
    return trim((string) ($book['cover_path'] ?? ''));
}

function bookStatusLabel(string $status, array $statusLabelMap): string
{
    return $statusLabelMap[$status] ?? $status;
}

function renderBookButton(array $book, array $statusLabelMap): void
{
    $cover = bookCoverPath($book);
    ?>
    <button
        type="button"
        class="trigger-book-modal cover-book"
        data-id="<?= e($book['id'] ?? '') ?>"
        data-title="<?= e($book['title'] ?? '') ?>"
        data-author="<?= e($book['author'] ?? '') ?>"
        data-year="<?= e($book['year'] ?? '') ?>"
        data-genre="<?= e($book['genre'] ?? '') ?>"
        data-pages="<?= e($book['pages'] ?? '') ?>"
        data-status-value="<?= e($book['status'] ?? '') ?>"
        data-status="<?= e(bookStatusLabel($book['status'] ?? '', $statusLabelMap)) ?>"
        data-quote="<?= e($book['notes'] ?? '') ?>"
        data-cover="<?= e($cover) ?>"
        data-volume-id="<?= e($book['google_volume_id'] ?? '') ?>"
    >
        <?php if ($cover !== ''): ?>
            <img src="<?= e($cover) ?>" alt="<?= e($book['title'] ?? '') ?>" loading="lazy">
        <?php else: ?>
            <span
                class="cover-book-fallback"
                style="background: linear-gradient(160deg, <?= e($book['cover_color'] ?? '#8a3d22') ?> 0%, #241109 100%);"
            ><?= e($book['title'] ?? '') ?></span>
        <?php endif; ?>
    </button>
    <?php
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
        <section class="top-row">
            <div class="hero-left">
                <span class="hero-eyebrow">RAYON PRINCIPAL</span>
                <h2>
                    Chaque livre a sa place.<br>
                    <em>Chaque histoire, son étagère.</em>
                </h2>
                <p>
                    Parcourez votre collection comme une vraie bibliothèque. Cliquez sur un livre pour ouvrir la fiche et le supprimer si besoin.
                </p>
                <a class="hero-cta" href="/library">🌍 Découvrir les livres libre service</a>
            </div>

            <div class="top-right-col">
                <div class="citation-card">
                    <img src="/assets/images/decor/citation-chaise.png" alt="Citation : une pièce sans livres est comme un corps sans âme — Cicéron" class="citation-card__image">
                </div>
            </div>
        </section>

        <?php if (!empty($search)): ?>
            <div class="search-back-wrap">
                <a href="/" class="search-back-btn">Retour aux étagères</a>
            </div>
        <?php endif; ?>

        <div class="shelf-toolbar-row">
            <div></div>
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
                    <details class="filter-dropdown">
                        <summary class="filter-chip filter-chip--dropdown">
                            Statut<?= $selectedStatus !== '' ? ' : ' . e($selectedStatus === 'to_read' ? 'À lire' : ($selectedStatus === 'reading' ? 'En cours' : ($selectedStatus === 'finished' ? 'Terminé' : $selectedStatus))) : '' ?>
                        </summary>
                        <div class="filter-dropdown-menu">
                            <a href="/?q=<?= urlencode($search) ?>&genre=<?= urlencode($selectedGenre) ?>&status=" class="filter-dropdown-item <?= $selectedStatus === '' ? 'active' : '' ?>">Tous statuts</a>
                            <a href="/?q=<?= urlencode($search) ?>&genre=<?= urlencode($selectedGenre) ?>&status=to_read" class="filter-dropdown-item <?= $selectedStatus === 'to_read' ? 'active' : '' ?>">À lire</a>
                            <a href="/?q=<?= urlencode($search) ?>&genre=<?= urlencode($selectedGenre) ?>&status=reading" class="filter-dropdown-item <?= $selectedStatus === 'reading' ? 'active' : '' ?>">En cours</a>
                            <a href="/?q=<?= urlencode($search) ?>&genre=<?= urlencode($selectedGenre) ?>&status=finished" class="filter-dropdown-item <?= $selectedStatus === 'finished' ? 'active' : '' ?>">Terminé</a>
                        </div>
                    </details>
                </div>
            </section>
            <div></div>
        </div>

        <div class="shelf-row-3col">
            <aside class="overview-column">
                <div class="overview-card">
                    <h3><span class="overview-title-icon">📊</span> Aperçu de la bibliothèque</h3>
                    <ul class="overview-list">
                        <li>
                            <span class="overview-icon overview-icon--books">📚</span>
                            <span class="overview-label">Total livres</span>
                            <span class="overview-value"><?= (int) ($stats['total_books'] ?? 0) ?></span>
                        </li>
                        <li>
                            <span class="overview-icon overview-icon--genres">🏷️</span>
                            <span class="overview-label">Genres</span>
                            <span class="overview-value"><?= (int) ($stats['total_genres'] ?? 0) ?></span>
                        </li>
                        <li>
                            <span class="overview-icon overview-icon--authors">✍️</span>
                            <span class="overview-label">Auteurs</span>
                            <span class="overview-value"><?= (int) ($stats['total_authors'] ?? 0) ?></span>
                        </li>
                        <li>
                            <span class="overview-icon overview-icon--reading">📖</span>
                            <span class="overview-label">En cours</span>
                            <span class="overview-value"><?= (int) ($stats['reading_books'] ?? 0) ?></span>
                        </li>
                        <li>
                            <span class="overview-icon overview-icon--finished">✅</span>
                            <span class="overview-label">Terminés</span>
                            <span class="overview-value"><?= (int) ($stats['finished_books'] ?? 0) ?></span>
                        </li>
                    </ul>
                </div>
            </aside>

            <section class="library-section">
                <div class="shelf-block">
                    <?php foreach ($shelves as $shelfIndex => $shelfBooks): ?>
                        <div class="shelf-row shelf-row--covers<?= empty($shelfBooks) ? ' shelf-row--empty' : '' ?>">
                            <?php if ($shelfIndex === 0): ?>
                                <div class="decor-book decor-book--plant">
                                    <img src="/assets/images/books/plant.png" alt="" aria-hidden="true">
                                </div>
                            <?php endif; ?>

                            <?php foreach ($shelfBooks as $book): ?>
                                <?php renderBookButton($book, $statusLabelMap); ?>
                            <?php endforeach; ?>

                            <?php if ($shelfIndex === 1): ?>
                                <div class="decor-book decor-book--globe">
                                    <img src="/assets/images/books/globe.png" alt="" aria-hidden="true">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="shelf-plank"></div>
                    <?php endforeach; ?>
                </div>

                <div class="shelf-actions">
                    <form action="/shelves/remove" method="post">
                        <button type="submit" class="hero-cta hero-cta--ghost hero-cta--icon" aria-label="Retirer une étagère">−</button>
                    </form>
                    <form action="/shelves/add" method="post">
                        <button type="submit" class="hero-cta hero-cta--ghost">+ Ajouter une étagère</button>
                    </form>
                </div>
            </section>

            <aside class="activity-column">
                <div class="activity-card">
                    <h3><span class="overview-title-icon">🕘</span> Activité récente</h3>

                    <?php if (empty($activity)): ?>
                        <p class="activity-empty">Aucune activité pour l'instant.</p>
                    <?php else: ?>
                        <ul class="activity-list">
                            <?php foreach ($activity as $event):
                                $eventCover = bookCoverPath($event);
                                $eventLabel = [
                                    'added' => 'Ajout de',
                                    'finished' => 'Lecture terminée :',
                                    'loaned' => 'Prêt de',
                                    'returned' => 'Retour de',
                                ][$event['type']] ?? '';
                                $ts = $event['event_at'] ?? null;
                                $timeAgo = '';
                                if ($ts) {
                                    $diff = time() - strtotime($ts);
                                    if ($diff < 3600) {
                                        $timeAgo = 'il y a ' . max(1, (int) floor($diff / 60)) . ' min';
                                    } elseif ($diff < 86400) {
                                        $timeAgo = 'il y a ' . (int) floor($diff / 3600) . ' h';
                                    } else {
                                        $days = (int) floor($diff / 86400);
                                        $timeAgo = $days === 1 ? 'hier' : "il y a {$days} jours";
                                    }
                                }
                            ?>
                                <li class="activity-item">
                                    <span class="activity-thumb" style="<?= $eventCover !== '' ? '' : 'background: linear-gradient(160deg, ' . e($event['cover_color'] ?? '#8a3d22') . ' 0%, #241109 100%);' ?>">
                                        <?php if ($eventCover !== ''): ?>
                                            <img src="<?= e($eventCover) ?>" alt="">
                                        <?php endif; ?>
                                    </span>
                                    <span class="activity-text">
                                        <?= e($eventLabel) ?> <strong><?= e($event['title'] ?? '') ?></strong>
                                        <span class="activity-time"><?= e($timeAgo) ?></span>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
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
                    <input type="hidden" name="book_id" id="book-modal-id" value="">

                    <form action="/books/update-status" method="post" class="status-change-form">
                        <label for="book-modal-status-select" class="visually-hidden">Statut</label>
                        <select id="book-modal-status-select" name="status" class="status-select">
                            <option value="to_read">À lire</option>
                            <option value="reading">En cours</option>
                            <option value="finished">Terminé</option>
                        </select>
                        <button type="submit" class="modal-action modal-action--primary">Changer le statut</button>
                    </form>

                    <form action="/books/delete" method="post" class="delete-book-form">
                        <button type="submit" class="modal-action modal-action--danger">Supprimer</button>
                    </form>

                    <form action="/loans/create" method="post" class="loan-form">
                        <label for="book-modal-borrower" class="visually-hidden">Emprunteur</label>
                        <input type="text" name="borrower" id="book-modal-borrower" placeholder="Nom de l'emprunteur" required>
                        <button type="submit" class="modal-action">Marquer comme prêté</button>
                    </form>

                    <a href="#" id="book-modal-read" class="modal-action" style="display:none;">Lire</a>
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
const statusSelect = document.getElementById('book-modal-status-select');
const statusForm = document.querySelector('.status-change-form');
const deleteForm = document.querySelector('.delete-book-form');
const loanForm = document.querySelector('.loan-form');
const readLink = document.getElementById('book-modal-read');

document.addEventListener('click', (event) => {
    document.querySelectorAll('.filter-dropdown[open]').forEach((dropdown) => {
        if (!dropdown.contains(event.target)) {
            dropdown.removeAttribute('open');
        }
    });
});

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
        modalStatus.textContent = button.dataset.status || status;
        if (statusSelect) {
            statusSelect.value = button.dataset.statusValue || '';
        }
        modalTitle.textContent = title;
        modalMeta.textContent = `${author} · ${year}`;
        modalGenre.textContent = genre;
        modalPages.textContent = pages;
        modalQuote.textContent = quote ? `« ${quote} »` : '';

        const volumeId = button.dataset.volumeId || '';
        if (readLink) {
            if (volumeId !== '') {
                readLink.href = '/library/read?volume_id=' + encodeURIComponent(volumeId);
                readLink.style.display = '';
            } else {
                readLink.style.display = 'none';
            }
        }

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

function attachBookIdOnSubmit(form) {
    if (!form) return;
    form.addEventListener('submit', (ev) => {
        const prev = form.querySelector('input[name="book_id"]');
        if (prev) prev.remove();

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'book_id';
        input.value = modalBookId ? modalBookId.value : '';
        form.appendChild(input);
    });
}

attachBookIdOnSubmit(statusForm);
attachBookIdOnSubmit(deleteForm);
attachBookIdOnSubmit(loanForm);

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