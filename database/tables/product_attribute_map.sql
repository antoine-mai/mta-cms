CREATE TABLE IF NOT EXISTS product_attribute_map (
    product_id INTEGER NOT NULL,
    attribute_value_id INTEGER NOT NULL,
    PRIMARY KEY (product_id, attribute_value_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_value_id) REFERENCES product_attribute_values(id) ON DELETE CASCADE
);
