# 📚 BookNest

Bibliothèque personnelle unifiée — suivi, emprunts et lecture en un seul outil.

BookNest réunit dans une seule base de données trois usages habituellement dispersés : le catalogage des livres possédés, le suivi des prêts consentis à des proches, et la lecture de livres du domaine public directement dans l'application, via l'API Gutendex (Project Gutenberg).

## Fonctionnalités

- 📖 Étagères visuelles avec suivi du statut de lecture (à lire / en cours / terminé)
- 🤝 Gestion des emprunts (prêter, marquer comme rendu)
- 🌍 Catalogue de livres du domaine public, avec lecteur intégré paginé
- 📊 Tableau de bord : statistiques et activité récente
- 🔐 Authentification JWT + session
- 📄 API documentée via Swagger/OpenAPI

## Stack technique

- **Backend** : PHP 8.2, architecture MVC développée sans framework
- **Base de données** : PostgreSQL 13
- **Cache** : Redis (client RESP réimplémenté au-dessus d'un socket TCP)
- **Conteneurisation** : Docker Compose (PHP-FPM, nginx, PostgreSQL, Redis)
- **Tests** : PHPUnit
- **CI/CD** : GitHub Actions → déploiement sur Render

## Installation locale

Prérequis : [Docker](https://www.docker.com/) (ou [Colima](https://github.com/abiosoft/colima) sur macOS) et Docker Compose.

```bash
git clone https://github.com/chaima-bekhaouda/booknest.git
cd booknest
cp .env.example .env   # puis renseigner les variables (voir ci-dessous)
docker compose up -d
```

Jouer les migrations sur une base neuve :

```bash
for f in database/migrations/*.sql; do
  docker compose exec -T db psql -U postgres -d booknest < "$f"
done
```

L'application est accessible sur **http://localhost:8000**.

### Variables d'environnement principales

```env
DB_HOST=db
DB_PORT=5432
DB_NAME=booknest
DB_USER=postgres
DB_PASSWORD=
REDIS_HOST=redis
REDIS_PORT=6379
JWT_SECRET=
```

## Tests

```bash
docker compose exec app vendor/bin/phpunit --testdox
```

## Documentation de l'API

Une fois l'application lancée, l'interface Swagger est disponible sur **http://localhost:8000/swagger/**.

## Déploiement

L'application est déployée sur [Render](https://render.com) via `render.yaml`. Le pipeline GitHub Actions (`.github/workflows/deploy.yml`) exécute la suite de tests à chaque envoi sur `main` et ne déclenche le déploiement que si les tests passent.

## Arborescence

```
app/
├── Config/         → connexion base de données, config OpenAPI
├── Controllers/    → Auth, Book, Dashboard, Home, Library, Loan
├── Core/           → routeur, requête/réponse maison
├── Middleware/     → authentification
├── Models/         → Book, User
├── Repositories/   → accès aux données par entité
├── Services/       → JWT, cache Redis, intégration Gutendex
└── Views/          → gabarits PHP
database/migrations/ → schéma SQL versionné
tests/                → suite PHPUnit
```

## Auteure

Développé par **Chaima Bekhaouda**, dans le cadre d'une alternance et d'un Bachelor Concepteur Développeur d'Applications à La Plateforme.