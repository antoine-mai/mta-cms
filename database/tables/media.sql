CREATE TABLE IF NOT EXISTS media (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50), -- image, video, document, etc.
    mime_type VARCHAR(100),
    file_size INTEGER, -- in bytes
    width INTEGER, -- for images
    height INTEGER, -- for images
    uploaded_by INTEGER NOT NULL,
    alt_text VARCHAR(255),
    caption TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
);
