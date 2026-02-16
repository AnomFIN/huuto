#!/bin/bash
# Huuto - File Permissions Setup Script
# Run this script after uploading files via FTP to set all required permissions
#
# Usage:
#   ./set-permissions.sh
# Or if you need sudo:
#   sudo ./set-permissions.sh
#
# This script assumes you're running it from the web root directory

echo "🚀 Setting file permissions for Huuto application..."
echo ""

# Get the current directory (should be web root)
WEB_ROOT=$(pwd)

echo "Working directory: $WEB_ROOT"
echo ""

# Set writable directories (777)
echo "📁 Setting writable directories (chmod 777)..."
chmod 777 "$WEB_ROOT/config/" 2>/dev/null && echo "  ✅ config/" || echo "  ❌ config/ - may need sudo"
chmod 777 "$WEB_ROOT/uploads/" 2>/dev/null && echo "  ✅ uploads/" || echo "  ❌ uploads/ - may need sudo"
chmod 777 "$WEB_ROOT/storage/" 2>/dev/null && echo "  ✅ storage/" || echo "  ❌ storage/ - may need sudo"
chmod 777 "$WEB_ROOT/logs/" 2>/dev/null && echo "  ✅ logs/" || echo "  ❌ logs/ - may need sudo"
echo ""

# Set security files (644)
echo "🔒 Setting security files (chmod 644)..."
chmod 644 "$WEB_ROOT/.htaccess" 2>/dev/null && echo "  ✅ .htaccess" || echo "  ❌ .htaccess - may need sudo"
chmod 644 "$WEB_ROOT/uploads/.htaccess" 2>/dev/null && echo "  ✅ uploads/.htaccess" || echo "  ❌ uploads/.htaccess - may need sudo"
chmod 644 "$WEB_ROOT/logs/.htaccess" 2>/dev/null && echo "  ✅ logs/.htaccess" || echo "  ❌ logs/.htaccess - may need sudo"
echo ""

# Set PHP files (644)
echo "📄 Setting PHP files (chmod 644)..."
find "$WEB_ROOT" -maxdepth 1 -name "*.php" -type f -exec chmod 644 {} \; 2>/dev/null
find "$WEB_ROOT" -name "*.php" -type f -path "*/app/*" -exec chmod 644 {} \; 2>/dev/null
find "$WEB_ROOT" -name "*.php" -type f -path "*/src/*" -exec chmod 644 {} \; 2>/dev/null
find "$WEB_ROOT" -name "*.php" -type f -path "*/auth/*" -exec chmod 644 {} \; 2>/dev/null
echo "  ✅ All PHP files set to 644"
echo ""

# Set standard directories (755)
echo "📂 Setting standard directories (chmod 755)..."
chmod 755 "$WEB_ROOT/app/" 2>/dev/null && echo "  ✅ app/" || echo "  ⚠️  app/ - may not exist"
chmod 755 "$WEB_ROOT/auth/" 2>/dev/null && echo "  ✅ auth/" || echo "  ⚠️  auth/ - may not exist"
chmod 755 "$WEB_ROOT/database/" 2>/dev/null && echo "  ✅ database/" || echo "  ⚠️  database/ - may not exist"
chmod 755 "$WEB_ROOT/src/" 2>/dev/null && echo "  ✅ src/" || echo "  ⚠️  src/ - may not exist"
chmod 755 "$WEB_ROOT/src/models/" 2>/dev/null && echo "  ✅ src/models/" || echo "  ⚠️  src/models/ - may not exist"
chmod 755 "$WEB_ROOT/src/views/" 2>/dev/null && echo "  ✅ src/views/" || echo "  ⚠️  src/views/ - may not exist"
chmod 755 "$WEB_ROOT/assets/" 2>/dev/null && echo "  ✅ assets/" || echo "  ⚠️  assets/ - may not exist"
chmod 755 "$WEB_ROOT/assets/css/" 2>/dev/null && echo "  ✅ assets/css/" || echo "  ⚠️  assets/css/ - may not exist"
chmod 755 "$WEB_ROOT/tests/" 2>/dev/null && echo "  ✅ tests/" || echo "  ⚠️  tests/ - may not exist"
echo ""

echo "✨ Permission setup complete!"
echo ""
echo "Next steps:"
echo "1. Visit your installation page (e.g., https://your-domain.com/asennus.php, replacing 'your-domain.com' with your actual domain)"
echo "2. Follow the installation wizard"
echo "3. After installation, optionally tighten config/ permissions to 755"
echo ""
echo "For troubleshooting, see DEPLOYMENT.md"
