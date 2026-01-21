-- 1. Création de la table propre
CREATE TABLE IF NOT EXISTS destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    population INT DEFAULT NULL,
    search_term VARCHAR(255),        -- Ex: "Tokyo, Japon" (Ce qui s'affichera)
    image_url VARCHAR(500) DEFAULT NULL, -- Pour tes futures bannières
    UNIQUE KEY unique_place (city, country)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Insertion de données PARFAITES (Top 20 destinations)
INSERT INTO destinations (city, country, search_term, image_url) VALUES 
('Paris', 'France', 'Paris, France', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=1200&q=80'),
('Tokyo', 'Japon', 'Tokyo, Japon', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=1200&q=80'),
('New York', 'États-Unis', 'New York, États-Unis', 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=1200&q=80'),
('Londres', 'Royaume-Uni', 'Londres, Royaume-Uni', 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=1200&q=80'),
('Rome', 'Italie', 'Rome, Italie', 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=1200&q=80'),
('Barcelone', 'Espagne', 'Barcelone, Espagne', 'https://images.unsplash.com/photo-1583422409516-2895a77efded?w=1200&q=80'),
('Sydney', 'Australie', 'Sydney, Australie', 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=1200&q=80'),
('Kyoto', 'Japon', 'Kyoto, Japon', 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?w=1200&q=80'),
('Bali', 'Indonésie', 'Bali, Indonésie', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=1200&q=80'),
('Los Angeles', 'États-Unis', 'Los Angeles, États-Unis', 'https://images.unsplash.com/photo-1534190239940-9ba8944ea261?w=1200&q=80'),
('Bangkok', 'Thaïlande', 'Bangkok, Thaïlande', 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=1200&q=80'),
('Berlin', 'Allemagne', 'Berlin, Allemagne', 'https://images.unsplash.com/photo-1560969184-10fe8719e047?w=1200&q=80'),
('Lisbonne', 'Portugal', 'Lisbonne, Portugal', 'https://images.unsplash.com/photo-1555881400-74d7acaacd81?w=1200&q=80'),
('Amsterdam', 'Pays-Bas', 'Amsterdam, Pays-Bas', 'https://images.unsplash.com/photo-1512470876302-ac68777ee22d?w=1200&q=80'),
('Prague', 'République Tchèque', 'Prague, République Tchèque', 'https://images.unsplash.com/photo-1519677100203-a0e668c92439?w=1200&q=80'),
('Dubaï', 'Émirats Arabes Unis', 'Dubaï, Émirats Arabes Unis', 'https://images.unsplash.com/photo-1512453979798-5ea932a23644?w=1200&q=80'),
('Montréal', 'Canada', 'Montréal, Canada', 'https://images.unsplash.com/photo-1519178173668-2fc94fdf89f9?w=1200&q=80'),
('Marrakech', 'Maroc', 'Marrakech, Maroc', 'https://images.unsplash.com/photo-1597212618440-806262de4f6b?w=1200&q=80'),
('Rio de Janeiro', 'Brésil', 'Rio de Janeiro, Brésil', 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325?w=1200&q=80'),
('Athènes', 'Grèce', 'Athènes, Grèce', 'https://images.unsplash.com/photo-1603565816030-6b389eeb23cb?w=1200&q=80');