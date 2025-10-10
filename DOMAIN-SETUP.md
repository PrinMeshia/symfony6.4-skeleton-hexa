# 🌐 Configuration d'un domaine local avec Docker

Ce guide vous explique comment configurer un domaine local (ex: `site.dev`) pour votre application Symfony au lieu d'utiliser `localhost:8000`.

## 🚀 Installation rapide

```bash
# Exécuter le script automatique
./scripts/setup-local-domain.sh
```

## 📋 Installation manuelle

### 1. Modifier le fichier hosts

Ajoutez ces lignes dans votre fichier `/etc/hosts` :

```bash
# Configuration pour site.dev
127.0.0.1    site.dev
127.0.0.1    www.site.dev
```

**Sur Linux/Mac :**
```bash
sudo nano /etc/hosts
```

**Sur Windows :**
```
C:\Windows\System32\drivers\etc\hosts
```

### 2. Configuration nginx

Le fichier `docker/nginx/default.conf` a été mis à jour pour supporter :
- `localhost`
- `site.dev`
- `www.site.dev`

### 3. Redémarrer Docker

```bash
docker-compose down
docker-compose up -d
```

## 🎯 Utilisation

Après configuration, vous pouvez accéder à votre application via :

- 🌐 **http://site.dev:9080** (domaine principal)
- 🌐 **http://www.site.dev:9080** (avec www)
- 🌐 **http://localhost:9080** (toujours disponible)

### URLs importantes :

- 📚 **API Documentation** : http://site.dev:9080/api/doc
- ❤️ **Health Check** : http://site.dev:9080/api/health
- 🔐 **Login** : http://site.dev:9080/api/auth/login

## ⚙️ Configuration avancée

### Variables d'environnement

Dans votre `.env.local`, vous pouvez utiliser :

```env
# URL de base de l'API
API_BASE_URL=http://site.dev:9080

# CORS pour le domaine local
CORS_ALLOW_ORIGIN=^https?://(site\.dev|www\.site\.dev|localhost|127\.0\.0\.1)(:[0-9]+)?$
```

### Autres domaines

Pour utiliser d'autres domaines (ex: `monapp.local`, `api.test`), modifiez :

1. **Fichier hosts** :
```
127.0.0.1    monapp.local
127.0.0.1    api.test
```

2. **Configuration nginx** (`docker/nginx/default.conf`) :
```nginx
server_name localhost site.dev www.site.dev monapp.local api.test;
```

## 🔧 Dépannage

### Le domaine ne fonctionne pas

1. **Vérifiez le fichier hosts** :
```bash
cat /etc/hosts | grep site.dev
```

2. **Testez la connectivité** :
```bash
ping site.dev
```

3. **Vérifiez les conteneurs Docker** :
```bash
docker-compose ps
```

4. **Redémarrez nginx** :
```bash
docker-compose restart nginx
```

### Erreurs de cache DNS

Si vous avez des problèmes de cache DNS :

**Linux/Mac :**
```bash
sudo systemctl flush-dns  # ou sudo dscacheutil -flushcache
```

**Windows :**
```cmd
ipconfig /flushdns
```

## 🌟 Avantages

- ✅ **URLs plus propres** : `site.dev` au lieu de `localhost:8000`
- ✅ **Simulation de production** : Environnement plus proche de la réalité
- ✅ **Partage facile** : Plus facile à partager avec l'équipe
- ✅ **SSL possible** : Facilite l'ajout de certificats SSL locaux
- ✅ **Sous-domaines** : Possibilité d'utiliser `api.site.dev`, `admin.site.dev`, etc.

## 🔒 Sécurité

⚠️ **Important** : Ces domaines ne fonctionnent que localement. En production, utilisez de vrais domaines avec des certificats SSL appropriés.

## 📚 Ressources

- [Documentation Symfony - Configuration](https://symfony.com/doc/current/configuration.html)
- [Documentation Docker Compose](https://docs.docker.com/compose/)
- [Configuration nginx](https://nginx.org/en/docs/)
