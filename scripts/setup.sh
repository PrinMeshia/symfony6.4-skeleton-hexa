#!/bin/bash

echo "🚀 Setting up Symfony Hexagonal Skeleton..."

# Install dependencies
echo "📦 Installing dependencies..."
composer install

# Create JWT keys
echo "🔑 Creating JWT keys..."
mkdir -p config/jwt
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:your_jwt_passphrase_here
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:your_jwt_passphrase_here

# Set permissions
chmod 600 config/jwt/private.pem
chmod 644 config/jwt/public.pem

# Create database
echo "🗄️ Creating database..."
php bin/console doctrine:database:create --if-not-exists

# Run migrations
echo "📊 Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Clear cache
echo "🧹 Clearing cache..."
php bin/console cache:clear

echo "✅ Setup complete!"
echo ""
echo "🌐 Start the application with:"
echo "   symfony serve"
echo "   or"
echo "   docker-compose up -d"
echo ""
echo "📚 API Documentation: http://localhost:8000/api/doc"
echo "🏥 Health Check: http://localhost:8000/api/health"
