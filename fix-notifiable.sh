#!/bin/bash
# Fix Submission model - add Notifiable trait for email notifications
# Run from repo root: bash fix-notifiable.sh

set -e

echo "🔧 Adding Notifiable trait to Submission model..."

php << 'PHPEOF'
<?php
$content = file_get_contents("app/Models/Submission.php");

// Add use statement for Notifiable if not present
if (strpos($content, 'use Illuminate\\Notifications\\Notifiable;') === false) {
    $content = str_replace(
        "<?php\n\nnamespace App\\Models;",
        "<?php\n\nnamespace App\\Models;\n\nuse Illuminate\\Notifications\\Notifiable;",
        $content
    );
}

// Add Notifiable trait to the class if not present
if (strpos($content, 'use Notifiable;') === false) {
    // Find the class declaration and add trait after it
    $content = preg_replace(
        '/class Submission extends Model\s*\n\s*\{/',
        "class Submission extends Model\n{\n    use Notifiable;",
        $content
    );
}

// Add routeNotificationForMail method before the closing brace if not present
if (strpos($content, 'function routeNotificationForMail') === false) {
    $method = <<<'METHOD'

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail(): ?string
    {
        return $this->customer_email;
    }
METHOD;

    // Insert before the last closing brace of the class
    $lastBrace = strrpos($content, '}');
    if ($lastBrace !== false) {
        $content = substr_replace($content, $method . "\n}", $lastBrace, 1);
    }
}

file_put_contents("app/Models/Submission.php", $content);
echo "✅ Fixed app/Models/Submission.php\n";
PHPEOF

echo ""
echo "============================================================"
echo "🎉 Notifiable trait added!"
echo "============================================================"
echo ""
echo "Run tests: php artisan test --compact"
echo "Then commit: git add -A && git commit -m 'fix: add Notifiable trait to Submission for email notifications' && git push"
