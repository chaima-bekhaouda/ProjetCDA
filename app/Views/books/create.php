<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un livre - BookNest</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/book-create.css">
</head>
<body>
<?php
$old = $old ?? [];
$errors = $errors ?? [];

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<div class="create-shell">
    <header class="create-header">
        <a href="/" class="back-link">← Retour aux étagères</a>
        <div class="create-brand">BookNest</div>
    </header>

    <main class="create-layout">
        <section class="create-form-panel">
            <p class="section-kicker">Nouveau livre</p>
            <h1>Ajouter à votre étagère</h1>
            <p class="section-text">
                Remplissez les détails, choisissez la couleur de la tranche, et déposez-le sur le rayon.
            </p>

            <form action="/books" method="post" enctype="multipart/form-data" class="book-form">
                <div class="form-field form-field--full">
                    <label for="cover">Couverture</label>
                    <input
                        id="cover"
                        name="cover"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                    >
                    <?php if (!empty($errors['cover'])): ?>
                        <small class="field-error"><?= e($errors['cover']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="title">Titre *</label>
                        <input id="title" name="title" type="text" value="<?= e($old['title'] ?? '') ?>" required>
                        <?php if (!empty($errors['title'])): ?>
                            <small class="field-error"><?= e($errors['title']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label for="author">Auteur *</label>
                        <input id="author" name="author" type="text" value="<?= e($old['author'] ?? '') ?>" required>
                        <?php if (!empty($errors['author'])): ?>
                            <small class="field-error"><?= e($errors['author']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label for="year">Année</label>
                        <input id="year" name="year" type="text" value="<?= e($old['year'] ?? '') ?>">
                        <?php if (!empty($errors['year'])): ?>
                            <small class="field-error"><?= e($errors['year']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label for="pages">Pages</label>
                        <input id="pages" name="pages" type="text" value="<?= e($old['pages'] ?? '') ?>">
                        <?php if (!empty($errors['pages'])): ?>
                            <small class="field-error"><?= e($errors['pages']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label for="genre">Genre</label>
                        <input id="genre" name="genre" type="text" value="<?= e($old['genre'] ?? '') ?>">
                    </div>

                    <div class="form-field">
                        <label for="status">Statut</label>
                        <select id="status" name="status">
                            <option value="to_read" <?= (($old['status'] ?? 'to_read') === 'to_read') ? 'selected' : '' ?>>À lire</option>
                            <option value="reading" <?= (($old['status'] ?? '') === 'reading') ? 'selected' : '' ?>>En cours</option>
                            <option value="finished" <?= (($old['status'] ?? '') === 'finished') ? 'selected' : '' ?>>Terminé</option>
                        </select>
                        <?php if (!empty($errors['status'])): ?>
                            <small class="field-error"><?= e($errors['status']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-field">
                    <label>Couleur de la tranche</label>
                    <div class="color-picker">
                        <?php
                        $colors = ['#8a3d22', '#2f6a5b', '#294a7a', '#4e3a78', '#9a6a32', '#e8dcc2', '#114a4e', '#c78645', '#b18d6b', '#4b6a3d'];
                        $selectedColor = $old['cover_color'] ?? '#8a3d22';
                        ?>
                        <?php foreach ($colors as $color): ?>
                            <label class="color-swatch">
                                <input
                                    type="radio"
                                    name="cover_color"
                                    value="<?= e($color) ?>"
                                    <?= $selectedColor === $color ? 'checked' : '' ?>
                                >
                                <span style="background: <?= e($color) ?>;"></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-field form-field--full">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="5" placeholder="Impressions, citation favorite, à qui vous l’avez prêté..."><?= e($old['notes'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">Ajouter à la bibliothèque</button>
                    <a href="/" class="cancel-btn">Annuler</a>
                </div>
            </form>
        </section>

        <aside class="preview-panel">
            <p class="preview-label">Aperçu</p>

            <div class="preview-card">
                <div class="preview-book-wrap">
                    <div id="preview-book" class="preview-book">
                        <span id="preview-title">Titre du livre</span>
                    </div>

                    <div class="preview-shelf"></div>
                </div>

                <div class="preview-meta">
                    <strong id="preview-author">Auteur</strong>
                </div>
            </div>
        </aside>
    </main>
</div>

<script>
const titleInput = document.getElementById('title');
const authorInput = document.getElementById('author');
const previewTitle = document.getElementById('preview-title');
const previewAuthor = document.getElementById('preview-author');
const previewBook = document.getElementById('preview-book');
const colorInputs = document.querySelectorAll('input[name="cover_color"]');

function refreshPreview() {
    previewTitle.textContent = titleInput.value.trim() || 'Titre du livre';
    previewAuthor.textContent = authorInput.value.trim() || 'Auteur';

    const selected = document.querySelector('input[name="cover_color"]:checked');
    const color = selected ? selected.value : '#8a3d22';
    previewBook.style.background = `linear-gradient(180deg, ${color} 0%, #241109 100%)`;
}

titleInput.addEventListener('input', refreshPreview);
authorInput.addEventListener('input', refreshPreview);
colorInputs.forEach((input) => input.addEventListener('change', refreshPreview));

refreshPreview();
</script>
</body>
</html>