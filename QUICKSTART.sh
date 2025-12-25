#!/bin/bash

# Store Management System - Quick Start Script
# This script will set up the application for first-time use

echo "🚀 Starting Store Management System Setup..."
echo ""
echo "ℹ️  This application uses CDN-based Tailwind CSS"
echo "   No Node.js or npm required!"
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    php artisan key:generate
else
    echo "✅ .env file already exists"
fi

# Install PHP dependencies
echo ""
echo "📦 Installing PHP dependencies..."
composer install

# Check if database is configured
echo ""
echo "⚙️  Please ensure your database is configured in .env file"
echo "   DB_DATABASE=store_management"
echo "   DB_USERNAME=your_username"
echo "   DB_PASSWORD=your_password"
echo ""
echo "Press Enter to continue once database is ready..."
read

# Run migrations
echo ""
echo "🗄️  Running database migrations..."
php artisan migrate

# Seed database
echo ""
echo "🌱 Seeding database with initial data..."
php artisan db:seed

# Create storage link
echo ""
echo "🔗 Creating storage symbolic link..."
php artisan storage:link

echo ""
echo "✨ Setup Complete!"
echo ""
echo "📋 Default Admin Credentials:"
echo "   Phone: 9999999999"
echo "   Password: admin123"
echo ""
echo "🚀 To start the development server, run:"
echo "   php artisan serve"
echo ""
echo "   Then visit: http://localhost:8000"
echo ""
echo "💡 No build step needed - Tailwind CSS loads from CDN!"
echo ""
echo "📖 For more information, see README.md"
echo ""
