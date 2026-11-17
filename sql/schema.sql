-- Импортировать в выбранную пустую базу MySQL через phpMyAdmin.
SET NAMES utf8mb4;

CREATE TABLE cities (
    name VARCHAR(50) PRIMARY KEY,
    position SMALLINT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE genres (
    slug VARCHAR(20) PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    position SMALLINT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(254) UNIQUE,
    password_hash VARCHAR(255),
    role ENUM('photographer','client') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE photographer_profiles (
    user_id INT UNSIGNED PRIMARY KEY,
    city VARCHAR(50) NOT NULL,
    price INT UNSIGNED NOT NULL DEFAULT 3000,
    experience SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    night BOOLEAN NOT NULL DEFAULT FALSE,
    video BOOLEAN NOT NULL DEFAULT FALSE,
    trip BOOLEAN NOT NULL DEFAULT FALSE,
    avatar VARCHAR(64) NOT NULL DEFAULT 'avatar1.jpg',
    about VARCHAR(1000) NOT NULL DEFAULT '',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (city) REFERENCES cities(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE photographer_genres (
    photographer_id INT UNSIGNED NOT NULL,
    genre VARCHAR(20) NOT NULL,
    position SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (photographer_id, genre),
    FOREIGN KEY (photographer_id) REFERENCES photographer_profiles(user_id) ON DELETE CASCADE,
    FOREIGN KEY (genre) REFERENCES genres(slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    photographer_id INT UNSIGNED NOT NULL,
    title VARCHAR(100) NOT NULL,
    price INT UNSIGNED NOT NULL,
    FOREIGN KEY (photographer_id) REFERENCES photographer_profiles(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE available_dates (
    photographer_id INT UNSIGNED NOT NULL,
    available_on DATE NOT NULL,
    PRIMARY KEY (photographer_id, available_on),
    FOREIGN KEY (photographer_id) REFERENCES photographer_profiles(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE works (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    photographer_id INT UNSIGNED NOT NULL,
    title VARCHAR(100) NOT NULL,
    genre VARCHAR(20) NOT NULL,
    sample_file VARCHAR(64),
    tags JSON,
    is_public BOOLEAN NOT NULL DEFAULT TRUE,
    allow_download BOOLEAN NOT NULL DEFAULT FALSE,
    views INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX feed (is_public, created_at, id),
    INDEX portfolio (photographer_id, is_public, id),
    FOREIGN KEY (photographer_id) REFERENCES photographer_profiles(user_id) ON DELETE CASCADE,
    FOREIGN KEY (genre) REFERENCES genres(slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE work_images (
    work_id INT UNSIGNED PRIMARY KEY,
    mime ENUM('image/jpeg','image/png') NOT NULL,
    width INT UNSIGNED NOT NULL,
    height INT UNSIGNED NOT NULL,
    image_data MEDIUMBLOB NOT NULL,
    thumbnail MEDIUMBLOB NOT NULL,
    FOREIGN KEY (work_id) REFERENCES works(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    photographer_id INT UNSIGNED NOT NULL,
    name VARCHAR(50) NOT NULL,
    contact VARCHAR(254) NOT NULL,
    shoot_on DATE NOT NULL,
    genre VARCHAR(20) NOT NULL,
    message VARCHAR(1000) NOT NULL,
    status ENUM('new','viewed','done') NOT NULL DEFAULT 'new',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX inbox (photographer_id, created_at, id),
    FOREIGN KEY (photographer_id) REFERENCES photographer_profiles(user_id) ON DELETE CASCADE,
    FOREIGN KEY (genre) REFERENCES genres(slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE favorites (
    user_id INT UNSIGNED NOT NULL,
    photographer_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, photographer_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (photographer_id) REFERENCES photographer_profiles(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE profile_views (
    photographer_id INT UNSIGNED NOT NULL,
    viewed_on DATE NOT NULL,
    total INT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (photographer_id, viewed_on),
    FOREIGN KEY (photographer_id) REFERENCES photographer_profiles(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE feedback_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(254) NOT NULL,
    message VARCHAR(1000) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO cities (name, position) VALUES
    ('Якутск', 1), ('Мирный', 2), ('Нерюнгри', 3), ('Покровск', 4);

INSERT INTO genres (slug, name, position) VALUES
    ('portrait', 'Портрет', 1), ('landscape', 'Пейзаж', 2), ('reportage', 'Репортаж', 3),
    ('still', 'Предметная', 4), ('wedding', 'Свадебная', 5), ('family', 'Семейная', 6),
    ('lovestory', 'Love Story', 7);
