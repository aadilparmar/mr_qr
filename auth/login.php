<?php
$pageTitle = 'Log In';
require_once __DIR__ . '/../includes/header.php';
if (isLoggedIn()) { header('Location: ' . url('/dashboard')); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? ''); $password = $_POST['password'] ?? '';
    if (!$login || !$password) { $error = 'Please fill in all fields'; }
    else { $result = loginUser($login, $password); if ($result['success']) { setFlash('success', 'Welcome back!'); header('Location: ' . url('/dashboard')); exit; } else { $error = $result['error']; } }
}
?>
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4 hero-gradient">
    <div class="w-full max-w-md animate-fade-in-up">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center mx-auto mb-5 shadow-glow"><i data-lucide="log-in" class="w-8 h-8 text-white"></i></div>
            <h1 class="font-display text-3xl font-bold mb-2 text-slate-900">Welcome Back</h1>
            <p class="text-slate-500">Log in to your QRCode Pro account</p>
        </div>
        <div class="bg-white rounded-2xl p-8 shadow-elevated border border-slate-100">
            <?php if ($error): ?>
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 flex-shrink-0"></i>
                <span class="text-sm text-rose-600 font-medium"><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>
            <form method="POST" class="space-y-5">
                <div><label class="block text-sm font-semibold text-slate-600 mb-2">Username or Email</label>
                <input type="text" name="login" required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" class="w-full px-4 py-3 input-field" placeholder="johndoe or john@example.com" autofocus></div>
                <div><label class="block text-sm font-semibold text-slate-600 mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 input-field" placeholder="Enter your password"></div>
                <button type="submit" class="w-full py-3.5 rounded-xl font-display font-bold btn-primary text-base">Log In</button>
            </form>
            <div class="mt-6 text-center"><p class="text-sm text-slate-500">Don't have an account? <a href="<?= url('/register') ?>" class="text-brand-600 hover:text-brand-700 font-semibold">Sign up free</a></p></div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
