CREATE TABLE IF NOT EXISTS product_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    short_description TEXT,
    description TEXT,
    image VARCHAR(255),
    parent_id INTEGER,
    attribute_set_id INTEGER,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES product_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_set_id) REFERENCES product_attribute_sets(id) ON DELETE SET NULL
);
