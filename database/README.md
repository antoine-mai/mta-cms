# MTA CMS Database

## Overview

This directory contains the database schema and migration files for MTA CMS.

## Database Structure

The CMS uses SQLite as its database engine. The database file is stored at:
```
/storage/database/cms.db
```

## Tables

### Core Tables
- **users** - User accounts and authentication
- **sessions** - User session management
- **settings** - Site-wide configuration (key-value store)

### CMS Tables
- **posts** - Blog posts and pages
- **categories** - Content categories (hierarchical)
- **tags** - Content tags
- **post_tags** - Many-to-many relationship between posts and tags
- **media** - Uploaded files and images

### Ecommerce Tables
- **products** - Product catalog
- **product_images** - Product image gallery
- **customers** - Customer information
- **orders** - Order records
- **order_items** - Order line items

## Running Migrations

### Initial Setup

To create the database and run the initial migration:

```bash
php bin/migrate.php
```

This will:
1. Create the database file if it doesn't exist
2. Run all table creation scripts
3. Insert default data (admin user, categories, settings)
4. Verify all tables were created successfully

### Default Credentials

After migration, you can log in with:
- **Username:** admin
- **Email:** admin@example.com
- **Password:** admin123

⚠️ **IMPORTANT:** Change the default password immediately after first login!

## Database Schema

### Users Table
Stores user accounts with roles (admin, editor, user) and authentication data.

### Posts Table
Stores all content (posts and pages) with:
- Title, slug, content, excerpt
- Author, category, tags
- Status (draft, published, archived)
- Publishing dates and view counts

### Products Table
Stores product information for ecommerce:
- Name, SKU, description
- Pricing (regular and sale price)
- Stock management
- Categories and featured status

### Orders Table
Stores customer orders with:
- Order number, status, payment status
- Customer information
- Billing and shipping addresses
- Totals (subtotal, tax, shipping, total)

## Indexes

The schema includes indexes on frequently queried columns for optimal performance:
- Post slugs, author, category, status
- Product slugs, SKU, category
- Order customer, status, order number
- Media type and uploader
- Session user and activity

## Backup

To backup the database:

```bash
cp storage/database/cms.db storage/database/cms_backup_$(date +%Y%m%d_%H%M%S).db
```

## Restore

To restore from backup:

```bash
cp storage/database/cms_backup_YYYYMMDD_HHMMSS.db storage/database/cms.db
```

## Development

### Adding New Tables

1. Add table creation SQL to `database/schema.sql`
2. Update the migration script if needed
3. Run migration to create the table

### Modifying Tables

For production systems, create migration scripts instead of modifying the schema directly.

## Database Service

The `App\Services\Database` class provides a PDO wrapper with helper methods:

```php
use App\Services\Database;

// Initialize (done in startup.php)
Database::init('/path/to/database.db');

// Query
$users = Database::fetchAll('SELECT * FROM users WHERE role = ?', ['admin']);

// Insert
Database::execute('INSERT INTO posts (title, content) VALUES (?, ?)', [$title, $content]);
$id = Database::lastInsertId();

// Transactions
Database::beginTransaction();
try {
    Database::execute('...');
    Database::execute('...');
    Database::commit();
} catch (Exception $e) {
    Database::rollBack();
}
```

## Security Notes

1. All queries use prepared statements to prevent SQL injection
2. Foreign keys are enabled for data integrity
3. Passwords are hashed using bcrypt
4. Session data is stored securely
5. Database file should not be web-accessible

## Performance Tips

1. Use indexes on frequently queried columns
2. Limit result sets with LIMIT clauses
3. Use transactions for bulk operations
4. Consider pagination for large datasets
5. Cache frequently accessed data
