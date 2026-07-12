<section class="page-panel">
    <div class="page-heading">
        <div>
            <h1>Livres</h1>
            <p class="page-description">Chaque livre a sa place sur l’étagère, avec son auteur et son statut marqué comme terminé ou à relire.</p>
        </div>
    </div>

    <?php if (empty($books)): ?>
        <p class="empty-note">Aucun livre trouvé dans votre collection pour le moment.</p>
    <?php else: ?>
        <div class="card-grid">
            <?php foreach ($books as $book): ?>
                <?php $status = $book['status'] ?? 'to_read'; ?>
                <?php $statusLabel = $status === 'finished' ? 'Terminé' : ($status === 'reading' ? 'En cours' : 'À lire'); ?>
                <article class="vintage-card">
                    <div class="vintage-card-header">
                        <span class="vintage-badge status-<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($statusLabel) ?></span>
                        <span class="vintage-genre"><?= htmlspecialchars($book['genre'] ?? 'Genre inconnu') ?></span>
                    </div>
                    <h3><?= htmlspecialchars($book['title'] ?? 'Titre inconnu') ?></h3>
                    <p class="meta"><?= htmlspecialchars($book['author'] ?? 'Auteur inconnu') ?></p>
                    <?php if (!empty($book['year'])): ?>
                        <p class="meta-small">Année : <?= htmlspecialchars($book['year']) ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>