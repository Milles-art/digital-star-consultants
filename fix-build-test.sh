#!/bin/bash
# Fix build + test issues
set -e

echo "🔧 Fixing build and test issues..."

# Fix 1: Install axios (missing dependency)
echo "📦 Installing axios..."
npm install axios

# Fix 2: Change button text to match test expectation
echo "📝 Updating button text..."
sed -i 's/Submit application/Start your request/g' resources/views/services/show.blade.php

echo ""
echo "============================================================"
echo "🎉 Fixes applied!"
echo "============================================================"
echo ""
echo "Next steps:"
echo "  npm run build"
echo "  php artisan test --compact"
echo "  git add -A && git commit -m 'fix: install axios, update button text' && git push"
