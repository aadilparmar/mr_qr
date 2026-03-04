<?php
/**
 * QRCode Pro - Database & App Configuration
 * Uses SQLite for zero-config setup
 */

// ─── BASE PATH ───
// Change this if you move the app to a different subdirectory
define('BASE_PATH', '/mr_qr_v1');

define('DB_PATH', __DIR__ . '/../data/qrcode_pro.db');
define('APP_NAME', 'QRCode Pro');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('GENERATED_DIR', __DIR__ . '/../generated/');
define('MAX_BULK_QR', 100);

/**
 * Helper: builds a full URL path with the base prefix
 * Usage: url('/dashboard')  →  /mr_qr_v1/dashboard
 *        url('/')           →  /mr_qr_v1/
 */
function url(string $path = '/'): string {
    $base = rtrim(BASE_PATH, '/');
    if ($path === '/') return $base . '/';
    return $base . '/' . ltrim($path, '/');
}

// Ensure directories exist
if (!is_dir(dirname(DB_PATH))) mkdir(dirname(DB_PATH), 0755, true);
if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
if (!is_dir(GENERATED_DIR)) mkdir(GENERATED_DIR, 0755, true);

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA journal_mode=WAL');
        $pdo->exec('PRAGMA foreign_keys=ON');
    }
    return $pdo;
}

function initDatabase(): void {
    $db = getDB();
    
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        full_name TEXT DEFAULT '',
        avatar TEXT DEFAULT '',
        plan TEXT DEFAULT 'free',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS qr_codes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        type TEXT NOT NULL,
        title TEXT DEFAULT '',
        content TEXT NOT NULL,
        filename TEXT NOT NULL,
        settings TEXT DEFAULT '{}',
        scans INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS bulk_jobs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        job_name TEXT DEFAULT '',
        total_codes INTEGER DEFAULT 0,
        zip_filename TEXT DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $db->exec("CREATE INDEX IF NOT EXISTS idx_qr_user ON qr_codes(user_id)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_qr_type ON qr_codes(type)");
}

// Auto-initialize
initDatabase();
