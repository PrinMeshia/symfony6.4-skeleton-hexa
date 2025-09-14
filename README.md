# Symfony Skeleton - Architecture Hexagonale

Ce projet est un squelette Symfony utilisant l'architecture hexagonale (Clean Architecture) pour créer des applications maintenables et testables.

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

## 🚀 Utilisation

### Installation

```bash
composer install
```

### Configuration de la base de données

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
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

## 📚 Avantages de cette architecture

- **Testabilité** : Chaque couche peut être testée indépendamment
- **Maintenabilité** : Séparation claire des responsabilités
- **Flexibilité** : Facile de changer d'implémentation (ex: Doctrine → MongoDB)
- **Évolutivité** : Ajout de nouvelles fonctionnalités sans impacter l'existant

## 🔧 Développement

### Ajouter une nouvelle entité

1. Créer l'entité dans `Domain/{Entity}/Entity/`
2. Créer les value objects dans `Domain/{Entity}/ValueObject/`
3. Créer l'interface repository dans `Domain/{Entity}/Repository/`
4. Créer les DTOs dans `Application/DTO/`
5. Créer les cas d'usage dans `Application/UseCase/`
6. Implémenter le repository dans `Infrastructure/Persistence/`
7. Créer le contrôleur dans `UI/Api/`
8. Configurer les services dans `config/services.yaml`
