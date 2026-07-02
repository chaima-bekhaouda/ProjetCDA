<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Ajouter un livre</title></head>
<body>
<h1>Ajouter un livre</h1>
<form method="post" action="/books/store">
    <input type="text" name="title" placeholder="Titre">
    <button type="submit">Enregistrer</button>
</form>
<a href="/books">Retour</a>
</body>
</html>