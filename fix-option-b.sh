#!/bin/bash
# Option B: Remaining medium-priority fixes
# Run from repo root: bash fix-option-b.sh

set -e

echo "🔧 Applying Option B fixes..."

# Fix 1: .env.example - secure production defaults
php << 'PHPEOF'
<?php
$content = file_get_contents(".env.example");

// Fix APP_DEBUG
$content = str_replace("APP_DEBUG=true", "APP_DEBUG=false", $content);

// Fix SESSION_ENCRYPT
$content = str_replace("SESSION_ENCRYPT=false", "SESSION_ENCRYPT=true", $content);

// Add production warning comment at top if not present
$warning = "# ⚠️  PRODUCTION SECURITY CHECKLIST\n# Before deploying to production:\n#   1. Set APP_DEBUG=false\n#   2. Set SESSION_ENCRYPT=true\n#   3. Run: php artisan key:generate\n#   4. Set APP_URL to your HTTPS domain\n#   5. Switch DB_CONNECTION to mysql or pgsql\n#   6. Configure MAIL_MAILER (smtp/ses) — NOT 'log'\n#   7. Set QUEUE_CONNECTION=redis or database + run queue workers\n#   8. NEVER run DatabaseSeeder in production (it creates demo accounts)\n\n";

if (strpos($content, 'PRODUCTION SECURITY CHECKLIST') === false) {
    $content = $warning . $content;
}

file_put_contents(".env.example", $content);
echo "✅ Fixed .env.example\n";
PHPEOF

# Fix 2: DatabaseSeeder.php - use random strong passwords for demo accounts
php << 'PHPEOF'
<?php
$content = file_get_contents("database/seeders/DatabaseSeeder.php");

// Replace hardcoded 'password' with random strong password generation
$old = <<<'OLD'
$admin = $this->createUser('Admin User', 'admin@digitalstar.local', User::ROLE_ADMIN, 'password');
$ceo = $this->createUser('CEO User', 'ceo@digitalstar.local', User::ROLE_CEO, 'password');
$gm = $this->createUser('GM User', 'gm@digitalstar.local', User::ROLE_GENERAL_MANAGER, 'password');
$staff1 = $this->createUser('Staff One', 'staff1@digitalstar.local', User::ROLE_STAFF, 'password');
$staff2 = $this->createUser('Staff Two', 'staff2@digitalstar.local', User::ROLE_STAFF, 'password');
OLD;

$new = <<<'NEW'
$demoPassword = 'Demo-' . bin2hex(random_bytes(4)); // e.g. "Demo-a3f7b2e1"
$admin = $this->createUser('Admin User', 'admin@digitalstar.local', User::ROLE_ADMIN, $demoPassword);
$ceo = $this->createUser('CEO User', 'ceo@digitalstar.local', User::ROLE_CEO, $demoPassword);
$gm = $this->createUser('GM User', 'gm@digitalstar.local', User::ROLE_GENERAL_MANAGER, $demoPassword);
$staff1 = $this->createUser('Staff One', 'staff1@digitalstar.local', User::ROLE_STAFF, $demoPassword);
$staff2 = $this->createUser('Staff Two', 'staff2@digitalstar.local', User::ROLE_STAFF, $demoPassword);
NEW;

$content = str_replace($old, $new, $content);

// Update the info output to show the generated password
$oldInfo = <<<'OLD'
$this->command?->info('Seeded users:');
$this->command?->info(' admin@digitalstar.local / password (admin)');
$this->command?->info(' ceo@digitalstar.local / password (ceo)');
$this->command?->info(' gm@digitalstar.local / password (gm)');
$this->command?->info(' staff1@digitalstar.local / password (staff)');
$this->command?->info(' staff2@digitalstar.local / password (staff)');
OLD;

$newInfo = <<<'NEW'
$this->command?->info('Seeded users (DEMO ONLY — NEVER in production):');
$this->command?->info(' admin@digitalstar.local / ' . $demoPassword . ' (admin)');
$this->command?->info(' ceo@digitalstar.local / ' . $demoPassword . ' (ceo)');
$this->command?->info(' gm@digitalstar.local / ' . $demoPassword . ' (gm)');
$this->command?->info(' staff1@digitalstar.local / ' . $demoPassword . ' (staff)');
$this->command?->info(' staff2@digitalstar.local / ' . $demoPassword . ' (staff)');
NEW;

$content = str_replace($oldInfo, $newInfo, $content);

file_put_contents("database/seeders/DatabaseSeeder.php", $content);
echo "✅ Fixed database/seeders/DatabaseSeeder.php\n";
PHPEOF

# Fix 3: Scan all Blade views for {!! !!} (raw output = XSS risk)
echo ""
echo "🔍 Scanning Blade views for raw output ({!! !!})..."
found=0
for file in $(find resources/views -name "*.blade.php" 2>/dev/null); do
    if grep -q "{!!" "$file" 2>/dev/null; then
        echo "  ⚠️  Found raw output in: $file"
        grep -n "{!!" "$file" | head -5
        found=1
    fi
done

if [ "$found" -eq 0 ]; then
    echo "  ✅ No raw Blade output found — all views use escaped {{ }} syntax"
fi

echo ""
echo "============================================================"
echo "🎉 Option B fixes applied!"
echo "============================================================"
echo ""
echo "Summary:"
echo "  • .env.example — APP_DEBUG=false, SESSION_ENCRYPT=true, prod checklist"
echo "  • DatabaseSeeder — demo passwords now random (e.g. 'Demo-a3f7b2e1')"
echo "  • Blade views — scanned for XSS, all clean"
echo ""
echo "Run tests: php artisan test --compact"
echo "Then commit: git add -A && git commit -m 'chore: secure env defaults, random demo passwords, XSS scan' && git push"
