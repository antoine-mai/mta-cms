#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

define('ROOT_DIR', dirname(__DIR__));

use App\Services\Database;

// Initialize database path
$dbPath = ROOT_DIR . '/storage/database/cms.db';
Database::init($dbPath);

echo "🔧 MTA CMS Database Migration\n";
echo "================================\n\n";

try {
    // Check if database already exists
    if (file_exists($dbPath)) {
        echo "⚠️  Database already exists at: $dbPath\n";
        echo "Do you want to recreate it? This will DELETE all existing data! (yes/no): ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($line) !== 'yes') {
            echo "❌ Migration cancelled.\n";
            exit(0);
        }
        
        unlink($dbPath);
        echo "🗑️  Old database deleted.\n\n";
    }

    // Run migration
    echo "📦 Creating database structure...\n";
    $schemaFile = ROOT_DIR . '/database/schema.sql';
    Database::runMigration($schemaFile);
    
    echo "✅ Database created successfully!\n";
    echo "📍 Location: $dbPath\n\n";
    
    // Verify tables
    echo "📋 Verifying tables...\n";
    $tables = [
        'users', 'posts', 'categories', 'tags', 'post_tags',
        'media', 'products', 'product_images', 'customers',
        'orders', 'order_items', 'settings', 'sessions'
    ];
    
    foreach ($tables as $table) {
        if (Database::tableExists($table)) {
            echo "  ✓ $table\n";
        } else {
            echo "  ✗ $table (MISSING!)\n";
        }
    }
    
    echo "\n✨ Migration completed successfully!\n";
    echo "\n📝 Default admin credentials:\n";
    echo "   Username: admin\n";
    echo "   Password: admin123\n";
    echo "   ⚠️  Please change the password after first login!\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
