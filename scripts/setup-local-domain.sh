#!/bin/bash

# =============================================================================
# 🚀 Script de configuration du domaine local site.dev
# =============================================================================

set -e

echo "🌐 Configuration du domaine local site.dev..."

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages colorés
print_message() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Vérifier si le script est exécuté en tant que root
if [[ $EUID -eq 0 ]]; then
   print_error "Ce script ne doit pas être exécuté en tant que root"
   exit 1
fi

# Configuration du fichier hosts
HOSTS_FILE="/etc/hosts"
DOMAIN_ENTRY="127.0.0.1    site.dev www.site.dev"

print_info "Configuration du fichier hosts..."

# Vérifier si l'entrée existe déjà
if grep -q "site.dev" "$HOSTS_FILE"; then
    print_warning "L'entrée pour site.dev existe déjà dans $HOSTS_FILE"
else
    print_info "Ajout de l'entrée dans $HOSTS_FILE..."
    echo "$DOMAIN_ENTRY" | sudo tee -a "$HOSTS_FILE" > /dev/null
    print_message "Entrée ajoutée avec succès"
fi

# Vérifier la configuration Docker
print_info "Vérification de la configuration Docker..."

if [ ! -f "docker-compose.yml" ]; then
    print_error "Fichier docker-compose.yml non trouvé"
    exit 1
fi

if [ ! -f "docker/nginx/default.conf" ]; then
    print_error "Fichier de configuration nginx non trouvé"
    exit 1
fi

# Vérifier si nginx est configuré pour site.dev
if grep -q "site.dev" "docker/nginx/default.conf"; then
    print_message "Configuration nginx déjà mise à jour"
else
    print_warning "Configuration nginx non trouvée pour site.dev"
    print_info "Veuillez exécuter ce script depuis la racine du projet"
fi

# Instructions finales
echo ""
print_message "Configuration terminée !"
echo ""
print_info "Pour utiliser votre domaine local :"
echo "1. Redémarrez vos conteneurs Docker :"
echo "   docker-compose down && docker-compose up -d"
echo ""
echo "2. Accédez à votre application via :"
echo "   🌐 http://site.dev:9080"
echo "   🌐 http://www.site.dev:9080"
echo "   🌐 http://localhost:9080 (toujours disponible)"
echo ""
print_info "URLs importantes :"
echo "   📚 API Documentation : http://site.dev:9080/api/doc"
echo "   ❤️  Health Check : http://site.dev:9080/api/health"
echo ""

# Test de connectivité
print_info "Test de connectivité..."
if ping -c 1 site.dev > /dev/null 2>&1; then
    print_message "Le domaine site.dev est accessible"
else
    print_warning "Le domaine site.dev n'est pas encore accessible"
    print_info "Redémarrez vos conteneurs Docker et réessayez"
fi

echo ""
print_message "🎉 Configuration terminée avec succès !"
