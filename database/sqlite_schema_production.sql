-- SQLite schema matching MySQL production database
-- Generated for development environment

CREATE TABLE IF NOT EXISTS auctions (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    category_id INTEGER,
    title TEXT NOT NULL,
    description TEXT,
    starting_price REAL DEFAULT 0.00,
    current_price REAL DEFAULT 0.00,
    reserve_price REAL,
    buy_now_price REAL,
    bid_increment REAL DEFAULT 1.00,
    start_time TEXT,
    end_time TEXT NOT NULL,
    status TEXT DEFAULT 'active',
    views INTEGER DEFAULT 0,
    location TEXT,
    condition_description TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
    map_coordinates TEXT,
    seller_commitment INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS auction_images (
    id INTEGER PRIMARY KEY,
    auction_id INTEGER NOT NULL,
    image_path TEXT,
    caption TEXT,
    display_order INTEGER,
    is_primary BOOLEAN DEFAULT 0,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS auction_metadata (
    id INTEGER PRIMARY KEY,
    auction_id INTEGER NOT NULL,
    metadata_key TEXT,
    metadata_value TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    slug TEXT,
    description TEXT
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY,
    username TEXT,
    email TEXT NOT NULL,
    password_hash TEXT,
    first_name TEXT,
    last_name TEXT,
    phone TEXT,
    address TEXT,
    city TEXT,
    postal_code TEXT,
    country TEXT DEFAULT 'Finland',
    is_verified INTEGER DEFAULT 0,
    avatar_path TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bids (
    id INTEGER PRIMARY KEY,
    auction_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    bid_amount REAL NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS watchlist (
    id INTEGER PRIMARY KEY,
    user_id INTEGER NOT NULL,
    auction_id INTEGER NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY,
    table_name TEXT,
    record_id INTEGER,
    action TEXT,
    user_id INTEGER,
    changes TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sqlite_sequence (id INTEGER PRIMARY KEY AUTOINCREMENT);
