<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'BookNest') ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f7fb; color: #1f2937; }
        .hero { min-height: 100vh; display: grid; place-items: center; padding: 2rem; }
        .card { max-width: 700px; background: white; padding: 3rem; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,.08); text-align: center; }
        h1 { margin: 0 0 1rem; font-size: 3rem; }
        p { font-size: 1.15rem; line-height: 1.7; }
    </style>
</head>
<body>
    <main class="hero">
        <section class="card">
            <h1><?= htmlspecialchars($heading ?? 'Bienvenue') ?></h1>
            <p><?= htmlspecialchars($subtitle ?? '') ?></p>
        </section>
    </main>
</body>
</html>
