<section class="page-panel">
    <div class="page-heading">
        <div>
            <h1>Prêts</h1>
            <p class="page-description">Suivez les livres prêtés, l’emprunteur et l’état du retour.</p>
        </div>
    </div>
    <?php if (empty($loans)): ?>
        <p class="empty-note">Aucun prêt enregistré pour l’instant.</p>
    <?php else: ?>
        <div class="card-grid">
            <?php foreach ($loans as $loan): ?>
                <?php $returned = !empty($loan['returned_at']); ?>
                <article class="vintage-card">
                    <div class="vintage-card-header">
                        <span class="vintage-badge <?= $returned ? 'status-finished' : 'status-reading' ?>"><?= $returned ? 'Retourné' : 'En prêt' ?></span>
                        <span class="vintage-genre"><?= htmlspecialchars($loan['author'] ?? 'Auteur inconnu') ?></span>
                    </div>
                    <h3><?= htmlspecialchars($loan['title'] ?? 'Titre inconnu') ?></h3>
                    <p class="meta">Emprunteur : <?= htmlspecialchars($loan['borrower'] ?? 'Inconnu') ?></p>
                    <p class="meta-small">Prêté le <?= htmlspecialchars(date('d/m/Y', strtotime($loan['lent_at']))) ?></p>
                    <?php if ($returned): ?>
                        <p class="meta-small">Retour le <?= htmlspecialchars(date('d/m/Y', strtotime($loan['returned_at']))) ?></p>
                    <?php else: ?>
                        <form action="/loans/return" method="post" class="loan-return-form">
                            <input type="hidden" name="book_id" value="<?= htmlspecialchars($loan['book_id'] ?? '') ?>">
                            <button type="submit" class="modal-action modal-action--primary">Marquer comme rendu</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
