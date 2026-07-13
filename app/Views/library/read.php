<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest — Lecture</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/reader.css?v=<?= @filemtime(__DIR__ . '/../../../public/assets/css/reader.css') ?>">
</head>
<body class="reader-body">
<header class="reader-topbar">
    <a href="/library" class="reader-back">← Étagères</a>

    <div class="reader-heading">
        <?php if (!empty($book)): ?>
            <h1><?= htmlspecialchars($book['title']) ?></h1>
            <p><?= htmlspecialchars($book['author']) ?><?php if (!empty($error) === false): ?> · En cours<?php endif; ?></p>
        <?php else: ?>
            <h1>Lecture</h1>
        <?php endif; ?>
    </div>

    <div class="reader-controls">
        <button type="button" class="reader-btn" id="font-decrease" aria-label="Diminuer la taille du texte">T −</button>
        <button type="button" class="reader-btn" id="font-increase" aria-label="Augmenter la taille du texte">T +</button>
        <button type="button" class="reader-btn reader-btn--icon" id="theme-toggle" aria-label="Basculer le mode sombre">🌙</button>
    </div>
</header>

<main class="reader-main">
    <?php if (!empty($error)): ?>
        <div class="reader-card reader-card--error">
            <h2><?= !empty($book['title']) ? htmlspecialchars($book['title']) : 'Livre indisponible' ?></h2>
            <p>Le texte intégral de ce livre n'a pas pu être chargé.</p>
            <?php if (!empty($book['preview_link'])): ?>
                <p><a href="<?= htmlspecialchars($book['preview_link']) ?>" target="_blank" rel="noopener" class="reader-btn reader-btn--primary">Voir sur Project Gutenberg</a></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php
        $totalPages = (int) $totalPages;
        $currentPage = (int) $currentPage;
        $progressPercent = $totalPages > 0 ? round(($currentPage / $totalPages) * 100) : 0;
        ?>
        <article class="reader-card">
            <div class="reader-card__head">
                <span class="reader-card__label"><?= htmlspecialchars(mb_strtoupper($book['title'])) ?></span>
                <span class="reader-card__page">Page <?= $currentPage ?> / <?= $totalPages ?></span>
            </div>

            <div class="reader-card__body" id="reader-text">
                <?php
                $pageContent = $pages[$currentPage - 1] ?? '';
                $paragraphs = preg_split('/\n\s*\n/', $pageContent) ?: [];
                ?>
                <?php foreach ($paragraphs as $paragraph): ?>
                    <p><?= nl2br(htmlspecialchars(trim($paragraph))) ?></p>
                <?php endforeach; ?>
            </div>

            <div class="reader-card__footer">· <?= $currentPage ?> ·</div>
        </article>

        <nav class="reader-nav">
            <?php if ($currentPage > 1): ?>
                <a href="/library/read?volume_id=<?= urlencode($volumeId) ?>&page=<?= $currentPage - 1 ?>" class="reader-nav__btn">← Précédent</a>
            <?php else: ?>
                <span class="reader-nav__btn reader-nav__btn--disabled">← Précédent</span>
            <?php endif; ?>

            <div class="reader-progress">
                <div class="reader-progress__bar" style="width: <?= $progressPercent ?>%;"></div>
            </div>

            <?php if ($currentPage < $totalPages): ?>
                <a href="/library/read?volume_id=<?= urlencode($volumeId) ?>&page=<?= $currentPage + 1 ?>" class="reader-nav__btn reader-nav__btn--primary">Suivant →</a>
            <?php else: ?>
                <form action="/library/read/end" method="post" style="margin:0;">
                    <input type="hidden" name="volume_id" value="<?= htmlspecialchars($volumeId) ?>">
                    <button type="submit" class="reader-nav__btn reader-nav__btn--primary">Terminer ✓</button>
                </form>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</main>

<script>
(function () {
    const root = document.documentElement;
    const textEl = document.getElementById('reader-text');
    const decBtn = document.getElementById('font-decrease');
    const incBtn = document.getElementById('font-increase');
    const themeBtn = document.getElementById('theme-toggle');

    const MIN_SIZE = 15;
    const MAX_SIZE = 26;
    const STEP = 1;

    function applyFontSize(size) {
        if (textEl) {
            textEl.style.fontSize = size + 'px';
        }
        localStorage.setItem('booknest_reader_font_size', String(size));
    }

    function currentFontSize() {
        const stored = parseInt(localStorage.getItem('booknest_reader_font_size') || '19', 10);
        return isNaN(stored) ? 19 : stored;
    }

    applyFontSize(currentFontSize());

    if (decBtn) {
        decBtn.addEventListener('click', () => {
            const next = Math.max(MIN_SIZE, currentFontSize() - STEP);
            applyFontSize(next);
        });
    }

    if (incBtn) {
        incBtn.addEventListener('click', () => {
            const next = Math.min(MAX_SIZE, currentFontSize() + STEP);
            applyFontSize(next);
        });
    }

    function applyTheme(theme) {
        document.body.classList.toggle('reader-body--dark', theme === 'dark');
        localStorage.setItem('booknest_reader_theme', theme);
        if (themeBtn) {
            themeBtn.textContent = theme === 'dark' ? '☀️' : '🌙';
        }
    }

    const storedTheme = localStorage.getItem('booknest_reader_theme') || 'light';
    applyTheme(storedTheme);

    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const isDark = document.body.classList.contains('reader-body--dark');
            applyTheme(isDark ? 'light' : 'dark');
        });
    }
})();
</script>
</body>
</html>