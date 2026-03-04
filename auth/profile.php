<?php
$pageTitle = 'My Profile';
require_once __DIR__ . '/../includes/header.php';
requireLogin();
$user = getCurrentUser();
$stats = getUserStats($user['id']);
$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $db = getDB();
    if ($action === 'update_profile') {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = 'Invalid email address'; }
        else {
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?"); $stmt->execute([$email, $user['id']]);
            if ($stmt->fetch()) { $error = 'Email already in use'; }
            else { $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?"); $stmt->execute([$fullName, $email, $user['id']]); $success = 'Profile updated successfully'; $user = getCurrentUser(); }
        }
    } elseif ($action === 'change_password') {
        $current = $_POST['current_password'] ?? ''; $newPass = $_POST['new_password'] ?? ''; $confirm = $_POST['confirm_password'] ?? '';
        if (!password_verify($current, $user['password'])) { $error = 'Current password is incorrect'; }
        elseif (strlen($newPass) < 6) { $error = 'New password must be at least 6 characters'; }
        elseif ($newPass !== $confirm) { $error = 'New passwords do not match'; }
        else { $hash = password_hash($newPass, PASSWORD_BCRYPT); $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?"); $stmt->execute([$hash, $user['id']]); $success = 'Password changed successfully'; }
    }
}
?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="font-display text-3xl font-bold mb-8 text-slate-900 animate-fade-in-up">My Profile</h1>
    <?php if ($error): ?>
    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-3 animate-fade-in-up"><i data-lucide="alert-circle" class="w-5 h-5 text-rose-500"></i><span class="text-sm text-rose-600 font-medium"><?= htmlspecialchars($error) ?></span></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="mb-6 p-4 rounded-xl bg-mint-50 border border-mint-200 flex items-center gap-3 animate-fade-in-up"><i data-lucide="check-circle" class="w-5 h-5 text-mint-500"></i><span class="text-sm text-mint-600 font-medium"><?= htmlspecialchars($success) ?></span></div>
    <?php endif; ?>
    <div class="grid md:grid-cols-3 gap-8">
        <div class="space-y-6">
            <div class="bg-white rounded-2xl p-6 text-center border border-slate-100 shadow-soft animate-fade-in-up delay-100">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-brand-400 to-lilac-400 flex items-center justify-center mx-auto mb-4 shadow-glow">
                    <span class="text-2xl font-bold text-white"><?= strtoupper(substr($user['username'], 0, 2)) ?></span></div>
                <h3 class="font-display font-bold text-lg text-slate-800"><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></h3>
                <p class="text-sm text-slate-500 mt-1">@<?= htmlspecialchars($user['username']) ?></p>
                <p class="text-xs text-slate-400 mt-2">Member since <?= date('M Y', strtotime($user['created_at'])) ?></p>
            </div>
            <div class="bg-white rounded-2xl p-6 space-y-4 border border-slate-100 shadow-soft animate-fade-in-up delay-200">
                <div class="flex justify-between items-center"><span class="text-sm text-slate-500">Total QR Codes</span><span class="font-display font-bold text-brand-600"><?= number_format($stats['total_codes']) ?></span></div>
                <div class="flex justify-between items-center"><span class="text-sm text-slate-500">Total Scans</span><span class="font-display font-bold text-lilac-500"><?= number_format($stats['total_scans']) ?></span></div>
                <div class="flex justify-between items-center"><span class="text-sm text-slate-500">Plan</span><span class="tag bg-brand-50 text-brand-600 uppercase"><?= $user['plan'] ?></span></div>
            </div>
        </div>
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-soft animate-fade-in-up delay-300">
                <h2 class="font-display text-xl font-bold mb-5 flex items-center gap-2 text-slate-800"><i data-lucide="user" class="w-5 h-5 text-brand-500"></i> Profile Information</h2>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-semibold text-slate-600 mb-2">Full Name</label><input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" class="w-full px-4 py-3 input-field"></div>
                        <div><label class="block text-sm font-semibold text-slate-600 mb-2">Username</label><input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled class="w-full px-4 py-3 input-field opacity-50 cursor-not-allowed"></div>
                    </div>
                    <div><label class="block text-sm font-semibold text-slate-600 mb-2">Email</label><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full px-4 py-3 input-field"></div>
                    <button type="submit" class="px-6 py-3 rounded-xl font-semibold btn-primary">Save Changes</button>
                </form>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-soft animate-fade-in-up delay-400">
                <h2 class="font-display text-xl font-bold mb-5 flex items-center gap-2 text-slate-800"><i data-lucide="lock" class="w-5 h-5 text-lilac-500"></i> Change Password</h2>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="change_password">
                    <div><label class="block text-sm font-semibold text-slate-600 mb-2">Current Password</label><input type="password" name="current_password" required class="w-full px-4 py-3 input-field"></div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-semibold text-slate-600 mb-2">New Password</label><input type="password" name="new_password" required class="w-full px-4 py-3 input-field" placeholder="Min 6 characters"></div>
                        <div><label class="block text-sm font-semibold text-slate-600 mb-2">Confirm New</label><input type="password" name="confirm_password" required class="w-full px-4 py-3 input-field"></div>
                    </div>
                    <button type="submit" class="px-6 py-3 rounded-xl font-semibold bg-white border-2 border-lilac-200 text-lilac-600 hover:bg-lilac-50 transition-all">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
