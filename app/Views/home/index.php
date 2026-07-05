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
    <!-- Top row: face covers -->
    <div class="shelf-row shelf-row--covers">
      <article class="cover-book"><img src="/assets/images/books/la-maison-da-cote.jpeg" alt="La Maison d'à côté"></article>
      <article class="cover-book"><img src="/assets/images/books/les-morsures-du-passe.jpeg" alt="Les Morsures du passé"></article>
      <article class="cover-book"><img src="/assets/images/books/preuves-damour.jpeg" alt="Preuves d'amour"></article>
      <article class="cover-book"><img src="/assets/images/books/arretez-moi.jpeg" alt="Arrêtez-moi"></article>
      <article class="cover-book"><img src="/assets/images/books/famille-parfaite.jpeg" alt="Famille parfaite"></article>
      <article class="cover-book"><img src="/assets/images/books/lumiere-noire.jpeg" alt="Lumière noire"></article>
    </div>

    <!-- shelf plank -->
    <div class="shelf-plank"></div>

    <!-- Middle row: spines (more books, real example titles) -->
    <div class="shelf-row shelf-row--spines">
      <!-- use real/known titles to fill visually -->
      <div class="spine-book tone-1"><span>La Maison d'à côté</span></div>
      <div class="spine-book tone-2"><span>Les Morsures du passé</span></div>
      <div class="spine-book tone-3"><span>Preuves d'amour</span></div>
      <div class="spine-book tone-4"><span>Arrêtez-moi</span></div>
      <div class="spine-book tone-5"><span>Famille parfaite</span></div>
      <div class="spine-book tone-6"><span>Lumière noire</span></div>

      <!-- additional real books to look authentic (examples) -->
      <div class="spine-book tone-2"><span>La Fille du train</span></div>
      <div class="spine-book tone-4"><span>Ne le dis à personne</span></div>
      <div class="spine-book tone-1"><span>Le Silence des agneaux</span></div>
      <div class="spine-book tone-5"><span>Shutter Island</span></div>
      <div class="spine-book tone-3"><span>La vérité sur l'affaire Harry</span></div>
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
</body>
</html>