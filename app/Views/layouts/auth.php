<?php
$title = $title ?? 'BookNest';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body class="auth-body">
    <main class="auth-shell">
        <?php require $view; ?>
    </main>
</body>
</html>