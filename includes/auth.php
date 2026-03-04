<?php
/**
 * Authentication Helper Functions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . url('/login'));
        exit;
    }
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function registerUser(string $username, string $email, string $password, string $fullName = ''): array {
    $db = getDB();
    
    // Check existing
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Username or email already exists'];
    }
    
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $hash, $fullName]);
    
    $userId = $db->lastInsertId();
    $_SESSION['user_id'] = $userId;
    
    return ['success' => true, 'user_id' => $userId];
}

function loginUser(string $login, string $password): array {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'error' => 'Invalid credentials'];
    }
    
    $_SESSION['user_id'] = $user['id'];
    return ['success' => true, 'user' => $user];
}

function logoutUser(): void {
    session_destroy();
    header('Location: ' . url('/login'));
    exit;
}

function getUserStats(int $userId): array {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM qr_codes WHERE user_id = ?");
    $stmt->execute([$userId]);
    $total = $stmt->fetch()['total'];
    
    $stmt = $db->prepare("SELECT COALESCE(SUM(scans), 0) as total_scans FROM qr_codes WHERE user_id = ?");
    $stmt->execute([$userId]);
    $scans = $stmt->fetch()['total_scans'];
    
    $stmt = $db->prepare("SELECT type, COUNT(*) as count FROM qr_codes WHERE user_id = ? GROUP BY type ORDER BY count DESC");
    $stmt->execute([$userId]);
    $byType = $stmt->fetchAll();
    
    return [
        'total_codes' => $total,
        'total_scans' => $scans,
        'by_type' => $byType
    ];
}
