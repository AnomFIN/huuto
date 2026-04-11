-- SQLite schema for huuto247
-- Basic tables for auction functionality

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users table  
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(50) DEFAULT 'Finland',
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Auctions table
CREATE TABLE IF NOT EXISTS auctions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    category_id INTEGER,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    short_summary VARCHAR(255),
    starting_price DECIMAL(10,2) DEFAULT 0.00,
    current_price DECIMAL(10,2) DEFAULT 0.00,
    reserve_price DECIMAL(10,2),
    buy_now_price DECIMAL(10,2),
    condition_id INTEGER,
    location VARCHAR(255),
    status VARCHAR(20) DEFAULT 'active',
    featured BOOLEAN DEFAULT FALSE,
    end_time TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Bids table
CREATE TABLE IF NOT EXISTS bids (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    auction_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Auction images table
CREATE TABLE IF NOT EXISTS auction_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    auction_id INTEGER NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    caption VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE
);

-- Sample categories
INSERT OR IGNORE INTO categories (id, name, slug, description) VALUES
(1, 'Elektroniikka', 'elektroniikka', 'Tietokoneet, puhelimet ja muut laitteet'),
(2, 'Kodinkoneet', 'kodinkoneet', 'Kodinkoneet ja kodin tarvikkeet'),
(3, 'Vaatteet', 'vaatteet', 'Vaatteet ja asusteet'),
(4, 'Urheilu', 'urheilu', 'Urheiluvälineet ja varusteet'),
(5, 'Kirjat', 'kirjat', 'Kirjat ja lehdet'),
(6, 'Lelut', 'lelut', 'Lelut ja pelit'),
(7, 'Autot', 'autot', 'Autot ja ajoneuvot'),
(8, 'Huonekalut', 'huonekalut', 'Huonekalut ja sisustus'),
(9, 'Musiikki', 'musiikki', 'Musiikkisoittimet ja levyt'),
(10, 'Muut', 'muut', 'Muut tavarat');

-- Test user
INSERT OR IGNORE INTO users (id, username, email, password_hash, first_name, last_name) VALUES
(1, 'testikayttaja', 'testi@huuto247.fi', 'hash123', 'Testi', 'Käyttäjä');