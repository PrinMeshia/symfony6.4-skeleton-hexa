.PHONY: help
.DEFAULT_GOAL := help

## —— 🐳 Docker ——————————————————————————————————————————————————————————————
build: ## Build les containers Docker
	docker compose build --no-cache

up: ## Lance les containers en arrière-plan
	docker compose up -d

down: ## Arrête les containers
	docker compose down

restart: down up ## Redémarre les containers

ps: ## Liste les containers actifs
	docker compose ps

logs: ## Affiche les logs des containers
	docker compose logs -f

logs-app: ## Affiche les logs du container app
	docker compose logs -f app

logs-nginx: ## Affiche les logs du container nginx
	docker compose logs -f nginx

logs-db: ## Affiche les logs de la base de données
	docker compose logs -f database

shell: ## Accède au shell du container app
	docker compose exec app bash

shell-root: ## Accède au shell du container app en root
	docker compose exec -u root app bash

clean: ## Supprime les containers, volumes et images
	docker compose down -v --remove-orphans
	docker system prune -af

## —— 🎵 Composer ———————————————————————————————————————————————————————————
composer-install: ## Installation des dépendances Composer
	docker compose exec app composer install

composer-update: ## Mise à jour des dépendances Composer
	docker compose exec app composer update

composer-require: ## Installe un package Composer (usage: make composer-require package=vendor/package)
	docker compose exec app composer require $(package)

composer-remove: ## Désinstalle un package Composer (usage: make composer-remove package=vendor/package)
	docker compose exec app composer remove $(package)

composer-dump: ## Génère l'autoload Composer optimisé
	docker compose exec app composer dump-autoload --optimize

## —— 🎶 Symfony ————————————————————————————————————————————————————————————
sf: ## Exécute une commande Symfony (usage: make sf cmd="debug:router")
	docker compose exec app php bin/console $(cmd)

cc: ## Vide le cache Symfony
	docker compose exec app php bin/console cache:clear

warmup: ## Préchauffe le cache
	docker compose exec app php bin/console cache:warmup

fix-perms: ## Corrige les permissions des dossiers var/
	docker compose exec -u root app chmod -R 777 var/

debug-router: ## Liste toutes les routes
	docker compose exec app php bin/console debug:router

debug-container: ## Liste tous les services du container
	docker compose exec app php bin/console debug:container

## —— 🗄️  Base de données ——————————————————————————————————————————————————
db-create: ## Crée la base de données
	docker compose exec app php bin/console doctrine:database:create --if-not-exists

db-drop: ## Supprime la base de données
	docker compose exec app php bin/console doctrine:database:drop --force --if-exists

db-migrate: ## Exécute les migrations
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

db-diff: ## Génère une nouvelle migration
	docker compose exec app php bin/console doctrine:migrations:diff

db-rollback: ## Annule la dernière migration
	docker compose exec app php bin/console doctrine:migrations:migrate prev --no-interaction

db-fixtures: ## Charge les fixtures
	docker compose exec app php bin/console doctrine:fixtures:load --no-interaction

db-validate: ## Valide le schéma de la base de données
	docker compose exec app php bin/console doctrine:schema:validate

db-reset: db-drop db-create db-migrate db-fixtures ## Reset complet de la BDD avec fixtures

## —— 🐘 PostgreSQL ————————————————————————————————————————————————————————
psql: ## Accède à la console PostgreSQL
	docker compose exec database psql -U app -d app

db-dump: ## Crée un dump de la base de données
	docker compose exec database pg_dump -U app app > backup_$$(date +%Y%m%d_%H%M%S).sql

db-restore: ## Restore un dump (usage: make db-restore file=backup.sql)
	docker compose exec -T database psql -U app -d app < $(file)

## —— ✅ Tests ——————————————————————————————————————————————————————————————
test: ## Lance tous les tests
	docker compose exec app php bin/phpunit

test-coverage: ## Lance les tests avec couverture de code
	docker compose exec app php bin/phpunit --coverage-html var/coverage

test-unit: ## Lance uniquement les tests unitaires
	docker compose exec app php bin/phpunit --testsuite=Unit

test-functional: ## Lance uniquement les tests fonctionnels
	docker compose exec app php bin/phpunit --testsuite=Functional

## —— 🔍 Qualité de code ———————————————————————————————————————————————————
phpstan: ## Analyse statique avec PHPStan
	docker compose exec app vendor/bin/phpstan analyse src --level=max

cs-fixer-dry: ## Vérifie le code style (sans modification)
	docker compose exec app vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fixer: ## Corrige le code style
	docker compose exec app vendor/bin/php-cs-fixer fix

lint: ## Vérifie la syntaxe des fichiers Twig et YAML
	docker compose exec app php bin/console lint:twig templates/
	docker compose exec app php bin/console lint:yaml config/

## —— 📦 Assets ————————————————————————————————————————————————————————————
assets-install: ## Installe les assets dans public/
	docker compose exec app php bin/console assets:install public --symlink

watch: ## Compile les assets en mode watch (si Webpack Encore)
	docker compose exec app npm run watch

build-assets: ## Compile les assets pour la production
	docker compose exec app npm run build

## —— 🚀 Installation ——————————————————————————————————————————————————————
install: build up composer-install db-create db-migrate ## Installation complète du projet
	@echo "✅ Installation terminée !"
	@echo "🌐 Application disponible sur http://localhost:8000"

setup: install db-fixtures ## Installation + fixtures
	@echo "✅ Setup complet terminé avec fixtures !"

## —— 📋 Aide ——————————————————————————————————————————————————————————————
help: ## Affiche cette aide
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'