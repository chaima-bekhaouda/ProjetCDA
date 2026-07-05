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
  <div class="shelf-block">
    <div class="shelf-row shelf-row--covers">
  <button
    type="button"
    class="cover-book trigger-book-modal"
    data-title="La Maison d'à côté"
    data-author="Lisa Gardner"
    data-year="2010"
    data-genre="Roman"
    data-pages="412"
    data-status="Terminé"
    data-quote="Un thriller tendu, intime et impossible à lâcher."
    data-cover="/assets/images/books/la-maison-da-cote.jpeg"
  >
    <img src="/assets/images/books/la-maison-da-cote.jpeg" alt="La Maison d'à côté">
  </button>

  <button
    type="button"
    class="cover-book trigger-book-modal"
    data-title="Les Morsures du passé"
    data-author="Auteur inconnu"
    data-year="2024"
    data-genre="Thriller"
    data-pages="368"
    data-status="À lire"
    data-quote="Une lecture sombre où le passé revient toujours frapper."
    data-cover="/assets/images/books/les-morsures-du-passe.jpeg"
  >
    <img src="/assets/images/books/les-morsures-du-passe.jpeg" alt="Les Morsures du passé">
  </button>

  <button
    type="button"
    class="cover-book trigger-book-modal"
    data-title="Preuves d'amour"
    data-author="Auteur inconnu"
    data-year="2022"
    data-genre="Roman"
    data-pages="320"
    data-status="En cours"
    data-quote="Un roman sensible sur les liens, les blessures et la réparation."
    data-cover="/assets/images/books/preuves-damour.jpeg"
  >
    <img src="/assets/images/books/preuves-damour.jpeg" alt="Preuves d'amour">
  </button>

  <button
    type="button"
    class="cover-book trigger-book-modal"
    data-title="Arrêtez-moi"
    data-author="Lisa Gardner"
    data-year="2015"
    data-genre="Thriller"
    data-pages="390"
    data-status="À lire"
    data-quote="Une tension constante et un doute qui s'installe dès les premières pages."
    data-cover="/assets/images/books/arretez-moi.jpeg"
  >
    <img src="/assets/images/books/arretez-moi.jpeg" alt="Arrêtez-moi">
  </button>

  <button
    type="button"
    class="cover-book trigger-book-modal"
    data-title="Famille parfaite"
    data-author="Auteur inconnu"
    data-year="2021"
    data-genre="Roman noir"
    data-pages="352"
    data-status="Terminé"
    data-quote="Sous l'apparence du calme, tout finit par se fissurer."
    data-cover="/assets/images/books/famille-parfaite.jpeg"
  >
    <img src="/assets/images/books/famille-parfaite.jpeg" alt="Famille parfaite">
  </button>

  <button
    type="button"
    class="cover-book trigger-book-modal"
    data-title="Lumière noire"
    data-author="Auteur inconnu"
    data-year="2023"
    data-genre="Policier"
    data-pages="288"
    data-status="Lire plus tard"
    data-quote="Une ambiance dense et hypnotique, entre enquête et nuit profonde."
    data-cover="/assets/images/books/lumiere-noire.jpeg"
  >
    <img src="/assets/images/books/lumiere-noire.jpeg" alt="Lumière noire">
  </button>
</div>
    

    <!-- shelf plank -->
    <div class="shelf-plank"></div>

    <!-- Middle row: book spines -->
    <div class="shelf-row shelf-row--spines">
      <div class="spine-book tone-1"><span>La Maison d'à côté</span></div>
<div class="spine-book tone-2"><span>Les Morsures du passé</span></div>
<div class="spine-book tone-3"><span>Preuves d'amour</span></div>
<div class="spine-book tone-4"><span>Arrêtez-moi</span></div>
<div class="spine-book tone-5"><span>Famille parfaite</span></div>
<div class="spine-book tone-6"><span>Lumière noire</span></div>
    </div>

    <div class="shelf-plank"></div>

    <!-- Bottom row: plant on left, books center, globe on right -->
    <div class="shelf-row shelf-row--decor">
      <div class="decor-book decor-book--plant">
        <img src="/assets/images/books/plant.png" alt="" aria-hidden="true">
      </div>

      <div class="spine-book tall tone-3"><span>La Maison d'à côté</span></div>
      <div class="spine-book tone-5"><span>Les Morsures du passé</span></div>
      <div class="spine-book tall tone-2"><span>Preuves d'amour</span></div>
      <div class="spine-book tone-4"><span>Arrêtez-moi</span></div>
      <div class="spine-book tall tone-1"><span>Famille parfaite</span></div>
      <div class="spine-book tone-6"><span>Lumière noire</span></div>

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
                <span id="book-modal-status" class="book-modal__status">Terminé</span>

                <h3 id="book-modal-title" class="book-modal__title">Titre du livre</h3>
                <p id="book-modal-meta" class="book-modal__meta">Auteur · 2001</p>

                <div class="book-modal__infos">
                    <div class="book-info-card">
                        <span class="book-info-label">Genre</span>
                        <span id="book-modal-genre" class="book-info-value">Roman</span>
                    </div>

                    <div class="book-info-card">
                        <span class="book-info-label">Pages</span>
                        <span id="book-modal-pages" class="book-info-value">544</span>
                    </div>
                </div>

                <blockquote id="book-modal-quote" class="book-modal__quote">
                    « Citation du livre »
                </blockquote>

                <div class="book-modal__actions">
                    <button type="button" class="modal-action modal-action--primary">Marquer en cours</button>
                    <button type="button" class="modal-action modal-action--ghost">Modifier</button>
                    <button type="button" class="modal-action modal-action--secondary">Lire le livre</button>
                </div>
            </div>
        </div>
    </div>
</dialog>

<script>
const bookModal = document.getElementById('book-modal');
const modalCloseBtn = document.getElementById('book-modal-close');

const modalCover = document.getElementById('book-modal-cover');
const modalStatus = document.getElementById('book-modal-status');
const modalTitle = document.getElementById('book-modal-title');
const modalMeta = document.getElementById('book-modal-meta');
const modalGenre = document.getElementById('book-modal-genre');
const modalPages = document.getElementById('book-modal-pages');
const modalQuote = document.getElementById('book-modal-quote');

document.querySelectorAll('.trigger-book-modal').forEach((button) => {
    button.addEventListener('click', () => {
        const title = button.dataset.title || 'Livre';
        const author = button.dataset.author || 'Auteur inconnu';
        const year = button.dataset.year || '—';
        const genre = button.dataset.genre || 'Genre inconnu';
        const pages = button.dataset.pages || '—';
        const status = button.dataset.status || 'À lire';
        const quote = button.dataset.quote || 'Aucune note disponible.';
        const cover = button.dataset.cover || '';

        modalCover.src = cover;
        modalCover.alt = title;

        modalStatus.textContent = status;
        modalTitle.textContent = title;
        modalMeta.textContent = `${author} · ${year}`;
        modalGenre.textContent = genre;
        modalPages.textContent = pages;
        modalQuote.textContent = `« ${quote} »`;

        bookModal.showModal();
    });
});

modalCloseBtn.addEventListener('click', () => {
    bookModal.close();
});

bookModal.addEventListener('click', (event) => {
    const panel = bookModal.querySelector('.book-modal__panel');
    if (!panel.contains(event.target)) {
        bookModal.close();
    }
});
</script>
</body>
</html>