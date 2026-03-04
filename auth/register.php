<?php
$pageTitle = 'Create Account';
require_once __DIR__ . '/../includes/header.php';
if (isLoggedIn()) { header('Location: ' . url('/dashboard')); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    if (strlen($username) < 3) $error = 'Username must be at least 3 characters';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Please enter a valid email';
    elseif (strlen($password) < 6) $error = 'Password must be at least 6 characters';
    elseif ($password !== $confirm) $error = 'Passwords do not match';
    else {
        $result = registerUser($username, $email, $password, $fullName);
        if ($result['success']) { setFlash('success', 'Account created! Welcome to QRCode Pro.'); header('Location: ' . url('/dashboard')); exit; }
        else { $error = $result['error']; }
    }
}
?>
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4 hero-gradient">
    <div class="w-full max-w-md animate-fade-in-up">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-500 to-lilac-500 flex items-center justify-center mx-auto mb-5 shadow-glow"><i data-lucide="user-plus" class="w-8 h-8 text-white"></i></div>
            <h1 class="font-display text-3xl font-bold mb-2 text-slate-900">Create Account</h1>
            <p class="text-slate-500">Start generating QR codes in seconds</p>
        </div>
        <div class="bg-white rounded-2xl p-8 shadow-elevated border border-slate-100">
            <?php if ($error): ?>
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 flex-shrink-0"></i>
                <span class="text-sm text-rose-600 font-medium"><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>
            <form method="POST" class="space-y-5">
                <div><label class="block text-sm font-semibold text-slate-600 mb-2">Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" class="w-full px-4 py-3 input-field" placeholder="John Doe"></div>
                <div><label class="block text-sm font-semibold text-slate-600 mb-2">Username <span class="text-rose-400">*</span></label>
                <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" class="w-full px-4 py-3 input-field" placeholder="johndoe"></div>
                <div><label class="block text-sm font-semibold text-slate-600 mb-2">Email <span class="text-rose-400">*</span></label>
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="w-full px-4 py-3 input-field" placeholder="john@example.com"></div>
                <div><label class="block text-sm font-semibold text-slate-600 mb-2">Password <span class="text-rose-400">*</span></label>
                <input type="password" name="password" required class="w-full px-4 py-3 input-field" placeholder="Min 6 characters"></div>
                <div><label class="block text-sm font-semibold text-slate-600 mb-2">Confirm Password <span class="text-rose-400">*</span></label>
                <input type="password" name="confirm_password" required class="w-full px-4 py-3 input-field" placeholder="Repeat password"></div>
                <button type="submit" class="w-full py-3.5 rounded-xl font-display font-bold btn-primary text-base">Create Account</button>
            </form>
            <div class="mt-6 text-center"><p class="text-sm text-slate-500">Already have an account? <a href="<?= url('/login') ?>" class="text-brand-600 hover:text-brand-700 font-semibold">Log in</a></p></div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
