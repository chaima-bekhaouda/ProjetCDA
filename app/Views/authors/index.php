<section class="page-panel">
    <div class="page-heading">
        <div>
            <h1>Auteurs</h1>
            <p class="page-description">Retrouvez les auteurs de votre bibliothèque et le nombre de titres qui les représentent.</p>
        </div>
    </div>

    <?php if (empty($authors)): ?>
        <p class="empty-note">Aucun auteur n’a encore été enregistré dans votre collection.</p>
    <?php else: ?>
        <div class="card-grid">
            <?php foreach ($authors as $author): ?>
                <article class="vintage-card">
                    <div class="vintage-card-header">
                        <span class="vintage-badge">Auteur</span>
                    </div>
                    <h3><?= htmlspecialchars($author['author_name']) ?></h3>
                    <p class="meta"><?= (int) $author['book_count'] ?> livre<?= $author['book_count'] > 1 ? 's' : '' ?></p>
                    <?php if (!empty($author['latest_title'])): ?>
                        <p class="meta-small">Dernier titre : <?= htmlspecialchars($author['latest_title']) ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>