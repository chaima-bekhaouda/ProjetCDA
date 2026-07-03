<div class="dashboard">
  <aside class="sidebar">
    <div class="brand">
      <img src="/assets/images/logobooknest.jpeg" alt="BookNest">
      <span>BookNest</span>
    </div>

    <nav class="nav">
      <a class="<?= $active === 'dashboard' ? 'active' : '' ?>" href="/dashboard">Dashboard</a>
      <a class="<?= $active === 'books' ? 'active' : '' ?>" href="/books">Livres</a>
      <a class="<?= $active === 'authors' ? 'active' : '' ?>" href="/authors">Auteurs</a>
      <a class="<?= $active === 'loans' ? 'active' : '' ?>" href="/loans">Prêts</a>
      <a class="<?= $active === 'reading-sessions' ? 'active' : '' ?>" href="/reading-sessions">Sessions</a>
      <a class="<?= $active === 'users' ? 'active' : '' ?>" href="/users">Utilisateurs</a>
      <a href="/logout">Déconnexion</a>
    </nav>

    <div class="sidebar-card">
      <p>"A room without books is like a body without a soul." — Cicero</p>
    </div>
  </aside>

  <main class="main">
    <div class="topbar">
      <div class="welcome">
        <h1>BookNest</h1>
      </div>

      <div class="userbox">
        <img src="/assets/images/users/avatar.png" alt="Bek Chae">
        <span>Bek Chae</span>
      </div>
    </div>

    <div class="content-grid">
      <section class="bookshelf">
        <div class="bookshelf-header">
          <div class="bookshelf-title">Dashboard</div>
        </div>

        <div class="bookshelf-inner">
          <div class="shelf">
            <div class="book-card">
              <img src="/assets/images/books/Naruto tome1.png" alt="Naruto tome 1">
            </div>
            <div class="book-card">
              <img src="/assets/images/books/seigneur des anneaux.png" alt="Seigneur des anneaux">
            </div>
            <div class="book-card">
              <img src="/assets/images/books/harry potter.png" alt="Harry Potter">
            </div>
            <div class="book-card">
              <img src="/assets/images/books/l'etranger.png" alt="L'étranger">
            </div>
          </div>

          <div class="shelf">
            <div class="book-card">
              <img src="/assets/images/books/1984.png" alt="1984">
            </div>
            <div class="book-card">
              <img src="/assets/images/books/le petit prince.png" alt="Le Petit Prince">
            </div>
          </div>
        </div>
      </section>


        <div class="panel">
          <h2>Bienvenue</h2>
          <p>Bienvenue dans ton espace bibliothèque.</p>
        </div>
      </aside>
    </div>
  </main>
</div>