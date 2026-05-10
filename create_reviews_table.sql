USE feane_events;

CREATE TABLE IF NOT EXISTS reviews (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    event_id     INT NOT NULL,
    author_name  VARCHAR(100) NOT NULL,
    rating       TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment      TEXT NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Sample reviews for existing events
INSERT INTO reviews (event_id, author_name, rating, comment) VALUES
(1, 'Sophie Martin', 5, 'Événement exceptionnel, très bien organisé !'),
(1, 'Lucas Bernard', 4, 'Super conférence, intervenants de qualité.'),
(1, 'Emma Dubois', 5, 'Une expérience inoubliable, je recommande !'),
(2, 'Thomas Petit', 4, 'Workshop très instructif et pratique.'),
(2, 'Léa Moreau', 3, 'Bien mais aurait pu être plus approfondi.');
