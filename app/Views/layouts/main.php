<?php
$title = $title ?? 'BookNest';
$active = $active ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body class="app-body">
<div class="app-layout">

    <section class="content">
        <?php require $view; ?>
    </section>
</div>
</body>
</html>