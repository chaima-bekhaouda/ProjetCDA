<section class="page-panel">
    <h1>Livres</h1>

    <div class="card-grid">
        <?php foreach ($books as $book): ?>
            <article class="small-card">
                <h3><?= htmlspecialchars($book['titre']) ?></h3>
                <p><?= htmlspecialchars(trim(($book['author_prenom'] ?? '') . ' ' . ($book['author_nom'] ?? ''))) ?></p>
                <span><?= htmlspecialchars($book['genre_libelle'] ?? 'Genre inconnu') ?></span>
            </article>
        <?php endforeach; ?>
    </div>
</section>