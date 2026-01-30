CREATE TABLE IF NOT EXISTS product_attribute_set_relations (
    set_id INTEGER NOT NULL,
    attribute_id INTEGER NOT NULL,
    sort_order INTEGER DEFAULT 0,
    PRIMARY KEY (set_id, attribute_id),
    FOREIGN KEY (set_id) REFERENCES product_attribute_sets(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES product_attributes(id) ON DELETE CASCADE
);
