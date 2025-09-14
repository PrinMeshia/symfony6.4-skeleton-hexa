# 🏗️ Symfony Hexagonal Skeleton - Version Complète

Un squelette Symfony utilisant l'architecture hexagonale (Clean Architecture) avec toutes les fonctionnalités modernes pour créer des applications robustes, maintenables et testables.

## 🏗️ Architecture

### Structure des couches

```
src/
├── Domain/           # Cœur métier (Business Logic)
│   └── User/
│       ├── Entity/       # Entités du domaine
│       ├── Repository/   # Interfaces des repositories
│       ├── Service/      # Services du domaine
│       └── ValueObject/  # Objets de valeur
├── Application/      # Cas d'usage (Use Cases)
│   ├── DTO/         # Data Transfer Objects
│   └── UseCase/     # Logique applicative
├── Infrastructure/  # Détails techniques
│   ├── Mailer/      # Services d'email
│   ├── Pdf/         # Services PDF
│   └── Persistence/ # Accès aux données
└── UI/              # Interfaces utilisateur
    ├── Api/         # Contrôleurs API REST
    ├── Cli/         # Commandes console
    └── Twig/        # Contrôleurs web
```

### Principes de l'architecture hexagonale

1. **Domain** : Contient la logique métier pure, sans dépendances externes
2. **Application** : Orchestre les cas d'usage en utilisant le domaine
3. **Infrastructure** : Implémente les détails techniques (base de données, email, etc.)
4. **UI** : Interface utilisateur (API, web, CLI)

### Flux de données

```
UI (Controller) → Application (UseCase) → Domain (Entity/Service) → Infrastructure (Repository)
```

## 🚀 Installation et Configuration

### Installation rapide

```bash
# Cloner le projet
git clone <your-repo>
cd symfony-skeleton

# Lancer le script de setup automatique
./scripts/setup.sh
```

### Installation manuelle

```bash
# Installer les dépendances
composer install

# Créer les clés JWT
mkdir -p config/jwt
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout

# Configurer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Vider le cache
php bin/console cache:clear
```

### Avec Docker

```bash
# Démarrer tous les services
docker-compose up -d

# Installer les dépendances dans le container
docker-compose exec app composer install

# Exécuter les migrations
docker-compose exec app php bin/console doctrine:migrations:migrate
```

### Exemple d'utilisation

#### Créer un utilisateur via API

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john.doe@example.com",
    "firstName": "John",
    "lastName": "Doe"
  }'
```

#### Récupérer un utilisateur

```bash
curl http://localhost:8000/api/users/{id}
```

## 📝 Exemple de code

### Entité du domaine

```php
// src/Domain/User/Entity/User.php
class User
{
    public function __construct(
        private UserId $id,
        private Email $email,
        private string $firstName,
        private string $lastName
    ) {}
}
```

### Cas d'usage

```php
// src/Application/UseCase/CreateUserUseCase.php
final class CreateUserUseCase
{
    public function execute(CreateUserRequest $request): UserResponse
    {
        // Logique applicative
    }
}
```

### Repository

```php
// src/Domain/User/Repository/UserRepositoryInterface.php
interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(UserId $id): ?User;
}
```

## 🧪 Tests

```bash
# Lancer les tests
php bin/phpunit
```

## ✨ Fonctionnalités incluses

### 🏗️ Architecture
- **Architecture Hexagonale** : Séparation claire des couches
- **Domain-Driven Design** : Entités, Value Objects, Services du domaine
- **Event Sourcing** : Événements du domaine avec listeners
- **CQRS** : Séparation des commandes et requêtes

### 🔒 Sécurité
- **Authentification JWT** : Tokens sécurisés
- **Validation Symfony** : Contraintes sur tous les DTOs
- **CORS** : Configuration pour les appels cross-origin
- **Rate Limiting** : Protection contre les abus

### 📊 Monitoring et Logging
- **Health Checks** : Endpoints de santé de l'application
- **Logging structuré** : Monolog avec contextes
- **Gestion d'erreurs** : Handler global d'exceptions
- **Métriques** : Surveillance des performances

### 🧪 Tests
- **Tests unitaires** : Couverture complète du domaine
- **Tests d'intégration** : Tests des APIs
- **PHPUnit** : Configuration optimisée
- **Fixtures** : Données de test

### 📚 Documentation
- **Swagger/OpenAPI** : Documentation interactive des APIs
- **Annotations** : Documentation automatique
- **Exemples** : Cas d'usage concrets

### 🐳 DevOps
- **Docker** : Containerisation complète
- **Docker Compose** : Services orchestrés
- **Nginx** : Serveur web optimisé
- **Scripts** : Automatisation des tâches

## 📚 Avantages de cette architecture

- **Testabilité** : Chaque couche peut être testée indépendamment
- **Maintenabilité** : Séparation claire des responsabilités
- **Flexibilité** : Facile de changer d'implémentation (ex: Doctrine → MongoDB)
- **Évolutivité** : Ajout de nouvelles fonctionnalités sans impacter l'existant
- **Sécurité** : Authentification et validation robustes
- **Monitoring** : Surveillance complète de l'application
- **Documentation** : APIs auto-documentées

## 🔧 Développement

### Commandes utiles

```bash
# Tests
php bin/phpunit                    # Lancer tous les tests
php bin/phpunit --testsuite=Unit   # Tests unitaires seulement
php bin/phpunit --testsuite=Integration  # Tests d'intégration

# Base de données
php bin/console doctrine:migrations:diff    # Créer une migration
php bin/console doctrine:migrations:migrate # Appliquer les migrations
php bin/console doctrine:fixtures:load      # Charger les fixtures

# Cache et optimisation
php bin/console cache:clear        # Vider le cache
php bin/console cache:warmup       # Préchauffer le cache

# Documentation
php bin/console api:openapi:export # Exporter la spec OpenAPI
```

### Ajouter une nouvelle entité

1. **Domain** : Créer l'entité dans `Domain/{Entity}/Entity/`
2. **Value Objects** : Créer les value objects dans `Domain/{Entity}/ValueObject/`
3. **Repository** : Créer l'interface dans `Domain/{Entity}/Repository/`
4. **Events** : Créer les événements dans `Domain/{Entity}/Event/`
5. **Application** : Créer les DTOs dans `Application/DTO/`
6. **Use Cases** : Créer les cas d'usage dans `Application/UseCase/`
7. **Infrastructure** : Implémenter le repository dans `Infrastructure/Persistence/`
8. **UI** : Créer le contrôleur dans `UI/Api/`
9. **Tests** : Créer les tests unitaires et d'intégration
10. **Config** : Configurer les services dans `config/services.yaml`

### Endpoints disponibles

- `POST /api/users` - Créer un utilisateur
- `GET /api/users/{id}` - Récupérer un utilisateur
- `POST /api/auth/login` - Authentification
- `GET /api/auth/me` - Profil utilisateur
- `GET /api/health` - Santé de l'application
- `GET /api/doc` - Documentation Swagger

## 🌐 URLs importantes

- **Application** : http://localhost:8000
- **API Documentation** : http://localhost:8000/api/doc
- **Health Check** : http://localhost:8000/api/health
- **Database** : localhost:3306 (MySQL)
- **Redis** : localhost:6379

## 📝 Variables d'environnement

Copiez `.env.example` vers `.env.local` et configurez :

```env
DATABASE_URL="mysql://user:password@localhost:3306/database"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_passphrase
CORS_ALLOW_ORIGIN=^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$
API_BASE_URL=http://localhost:8000
```
