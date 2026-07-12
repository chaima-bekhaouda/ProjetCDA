<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest — Lecture</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="stylesheet" href="/assets/css/library.css">
</head>
<body class="library-page">
<div class="home-shell">
    <header class="home-header">
        <div class="header-left">
            <div class="brand">
                <img src="/assets/images/logobooknest.png" alt="Logo BookNest" class="brand-logo">
                <div class="brand-text">
                    <h1>BookNest</h1>
                    <p>Lecture</p>
                </div>
            </div>
        </div>

        <div class="header-right">
            <?php if (empty($error)): ?>
                <form action="/library/read/end" method="post" style="display:inline;">
                    <input type="hidden" name="volume_id" value="<?= htmlspecialchars($volumeId) ?>">
                    <button type="submit" class="library-btn">Terminer la lecture</button>
                </form>
            <?php endif; ?>
            <a class="action-btn" href="/library">← Retour au catalogue</a>
        </div>
    </header>

    <main class="home-main">
        <?php if (!empty($error)): ?>
            <section class="library-intro">
                <h2><?= !empty($book['title']) ? htmlspecialchars($book['title']) : 'Livre indisponible' ?></h2>
                <p>Le texte intégral de ce livre n'a pas pu être chargé.</p>
                <?php if (!empty($book['preview_link'])): ?>
                    <p><a href="<?= htmlspecialchars($book['preview_link']) ?>" target="_blank" rel="noopener" class="library-btn library-btn--primary" style="display:inline-block; width:auto; padding:10px 18px;">Voir sur Project Gutenberg</a></p>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <section class="library-intro">
                <h2><?= htmlspecialchars($book['title']) ?></h2>
                <p><?= htmlspecialchars($book['author']) ?> — Page <?= (int) $currentPage ?> / <?= (int) $totalPages ?></p>
            </section>

            <section class="reader-wrap">
                <article class="reader-page">
                    <?php
                    $pageContent = $pages[$currentPage - 1] ?? '';
                    $paragraphs = preg_split('/\n\s*\n/', $pageContent) ?: [];
                    ?>
                    <?php foreach ($paragraphs as $paragraph): ?>
                        <p><?= nl2br(htmlspecialchars(trim($paragraph))) ?></p>
                    <?php endforeach; ?>
                </article>

                <nav class="reader-nav">
                    <?php if ($currentPage > 1): ?>
                        <a href="/library/read?volume_id=<?= urlencode($volumeId) ?>&page=<?= $currentPage - 1 ?>" class="library-btn">← Page précédente</a>
                    <?php else: ?>
                        <span></span>
                    <?php endif; ?>

                    <span class="reader-page-indicator">Page <?= (int) $currentPage ?> / <?= (int) $totalPages ?></span>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="/library/read?volume_id=<?= urlencode($volumeId) ?>&page=<?= $currentPage + 1 ?>" class="library-btn library-btn--primary">Page suivante →</a>
                    <?php else: ?>
                        <span></span>
                    <?php endif; ?>
                </nav>
            </section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>