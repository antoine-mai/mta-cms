-- Default Admin User (password: admin123)
INSERT OR IGNORE INTO users (id, username, email, password_hash, display_name, role, status) 
VALUES (1, 'admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 'active');

-- Default Post Categories
INSERT OR IGNORE INTO post_categories (id, name, slug, description) 
VALUES 
    (1, 'Uncategorized', 'uncategorized', 'Default category'),
    (2, 'News', 'news', 'News and updates'),
    (3, 'Blog', 'blog', 'Blog posts');

-- Default Product Categories
INSERT OR IGNORE INTO product_categories (id, name, slug, short_description, description) 
VALUES 
    (1, 'General Products', 'general-products', 'Default product category', 'All general products');

-- Default Settings
INSERT OR IGNORE INTO settings (setting_key, setting_value, setting_type, description) 
VALUES 
    ('site_name', 'MTA CMS', 'string', 'Website name'),
    ('site_description', 'A modern CMS platform', 'string', 'Website description'),
    ('posts_per_page', '10', 'number', 'Number of posts per page'),
    ('enable_comments', 'true', 'boolean', 'Enable comments on posts'),
    ('currency', 'USD', 'string', 'Default currency for ecommerce'),
    ('tax_rate', '0', 'number', 'Tax rate percentage');
