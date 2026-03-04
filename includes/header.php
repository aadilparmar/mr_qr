<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'QRCode Pro' ?> — QRCode Pro</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#EEF2FF',100:'#E0E7FF',200:'#C7D2FE',300:'#A5B4FC',400:'#818CF8',500:'#6366F1',600:'#4F46E5',700:'#4338CA',800:'#3730A3',900:'#312E81' },
                        coral:  { 100:'#FFE4DE',200:'#FECACA',400:'#F87171',500:'#EF4444' },
                        mint:   { 50:'#ECFDF5',100:'#D1FAE5',200:'#A7F3D0',400:'#34D399',500:'#10B981' },
                        peach:  { 50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',400:'#FB923C',500:'#F97316' },
                        sky:    { 50:'#F0F9FF',100:'#E0F2FE',200:'#BAE6FD',400:'#38BDF8',500:'#0EA5E9' },
                        lilac:  { 50:'#FAF5FF',100:'#F3E8FF',200:'#E9D5FF',400:'#C084FC',500:'#A855F7' },
                        rose:   { 50:'#FFF1F2',100:'#FFE4E6',200:'#FECDD3',400:'#FB7185',500:'#F43F5E' },
                        slate:  { 50:'#F8FAFC',100:'#F1F5F9',200:'#E2E8F0',300:'#CBD5E1',400:'#94A3B8',500:'#64748B',600:'#475569',700:'#334155',800:'#1E293B',900:'#0F172A' }
                    },
                    fontFamily: {
                        display: ['"Outfit"', 'system-ui', 'sans-serif'],
                        body:    ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        mono:    ['"JetBrains Mono"', 'monospace']
                    },
                    boxShadow: {
                        'soft':'0 2px 15px -3px rgba(0,0,0,0.05), 0 2px 6px -2px rgba(0,0,0,0.03)',
                        'card':'0 4px 25px -5px rgba(0,0,0,0.06), 0 2px 10px -3px rgba(0,0,0,0.04)',
                        'elevated':'0 10px 40px -10px rgba(0,0,0,0.08), 0 4px 15px -5px rgba(0,0,0,0.04)',
                        'glow':'0 0 30px -5px rgba(99,102,241,0.15)',
                    }
                }
            }
        }
    </script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FAFAFC; color: #1E293B; }
        h1,h2,h3,h4,h5,h6 { font-family: 'Outfit', sans-serif; }
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
        
        .bg-pattern {
            background-image: radial-gradient(circle at 1px 1px, rgba(99,102,241,0.03) 1px, transparent 0);
            background-size: 32px 32px;
        }
        .hero-gradient {
            background: 
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(199,210,254,0.5), transparent),
                radial-gradient(ellipse 60% 50% at 80% 20%, rgba(233,213,255,0.4), transparent),
                radial-gradient(ellipse 50% 40% at 50% 80%, rgba(186,230,253,0.3), transparent),
                linear-gradient(180deg, #FAFAFC 0%, #F0F1FF 100%);
        }
        .glass { background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border: 1px solid rgba(226,232,240,0.8); box-shadow: 0 4px 25px -5px rgba(0,0,0,0.06); }
        .card-hover { transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 15px 40px -10px rgba(0,0,0,0.1); }
        .input-field { background: #FFF; border: 1.5px solid #E2E8F0; color: #1E293B; transition: all 0.2s; border-radius: 12px; }
        .input-field:focus { border-color: #818CF8; box-shadow: 0 0 0 3px rgba(129,140,248,0.12); outline: none; }
        .input-field::placeholder { color: #94A3B8; }
        .qr-preview { background: white; border-radius: 16px; padding: 20px; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 15px -3px rgba(0,0,0,0.08); }
        .nav-active { background: #EEF2FF; color: #4F46E5; font-weight: 600; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #F1F5F9; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
        
        @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        @keyframes scaleIn { from { opacity:0; transform:scale(0.95); } to { opacity:1; transform:scale(1); } }
        
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
        .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
        .animate-slide-down { animation: slideDown 0.4s ease-out forwards; }
        .animate-float { animation: float 4s ease-in-out infinite; }
        .animate-scale-in { animation: scaleIn 0.4s ease-out forwards; }
        .delay-100 { animation-delay:0.1s; opacity:0; }
        .delay-200 { animation-delay:0.2s; opacity:0; }
        .delay-300 { animation-delay:0.3s; opacity:0; }
        .delay-400 { animation-delay:0.4s; opacity:0; }
        .delay-500 { animation-delay:0.5s; opacity:0; }
        .delay-600 { animation-delay:0.6s; opacity:0; }
        
        .gradient-text { background: linear-gradient(135deg, #6366F1, #8B5CF6, #06B6D4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .btn-primary { background: linear-gradient(135deg, #6366F1, #4F46E5); color: white; font-weight: 600; border-radius: 12px; transition: all 0.3s; box-shadow: 0 4px 15px -3px rgba(99,102,241,0.4); }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px -3px rgba(99,102,241,0.5); }
        .dropdown-menu { opacity:0; visibility:hidden; transform:translateY(8px) scale(0.97); transition: all 0.2s cubic-bezier(0.4,0,0.2,1); }
        .group:hover .dropdown-menu { opacity:1; visibility:visible; transform:translateY(0) scale(1); }
        .tag { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:8px; font-size:12px; font-weight:600; }
    </style>
</head>
<body class="min-h-screen bg-pattern">

<?php if ($flash): ?>
<div id="flash-message" class="fixed top-4 right-4 z-50 max-w-sm animate-slide-down">
    <div class="rounded-xl p-4 flex items-center gap-3 shadow-elevated border
        <?= $flash['type'] === 'success' ? 'bg-mint-50 border-mint-200' : 'bg-rose-50 border-rose-200' ?>">
        <i data-lucide="<?= $flash['type'] === 'success' ? 'check-circle' : 'alert-circle' ?>" 
           class="w-5 h-5 <?= $flash['type'] === 'success' ? 'text-mint-500' : 'text-rose-500' ?> flex-shrink-0"></i>
        <span class="text-sm font-medium text-slate-700"><?= htmlspecialchars($flash['message']) ?></span>
        <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-slate-400 hover:text-slate-600">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
</div>
<script>setTimeout(() => document.getElementById('flash-message')?.remove(), 5000)</script>
<?php endif; ?>

<!-- Navigation -->
<nav class="fixed top-0 left-0 right-0 z-40 bg-white/80 backdrop-blur-xl border-b border-slate-200/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16">
            <a href="<?= url('/') ?>" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center shadow-sm group-hover:shadow-glow transition-shadow duration-300">
                    <i data-lucide="qr-code" class="w-5 h-5 text-white"></i>
                </div>
                <span class="font-display font-bold text-lg tracking-tight text-slate-800">QR<span class="text-brand-500">Code</span> Pro</span>
            </a>

            <?php if ($currentUser): ?>
            <div class="hidden md:flex items-center gap-1">
                <a href="<?= url('/dashboard') ?>" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all hover:bg-brand-50 <?= $currentPage === 'dashboard' ? 'nav-active' : 'text-slate-500 hover:text-slate-700' ?>">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 inline mr-1.5"></i>Dashboard</a>
                <a href="<?= url('/generate') ?>" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all hover:bg-brand-50 <?= $currentPage === 'generate' ? 'nav-active' : 'text-slate-500 hover:text-slate-700' ?>">
                    <i data-lucide="plus-circle" class="w-4 h-4 inline mr-1.5"></i>Generate</a>
                <a href="<?= url('/history') ?>" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all hover:bg-brand-50 <?= $currentPage === 'history' ? 'nav-active' : 'text-slate-500 hover:text-slate-700' ?>">
                    <i data-lucide="history" class="w-4 h-4 inline mr-1.5"></i>History</a>
                <a href="<?= url('/bulk') ?>" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all hover:bg-brand-50 <?= $currentPage === 'bulk' ? 'nav-active' : 'text-slate-500 hover:text-slate-700' ?>">
                    <i data-lucide="layers" class="w-4 h-4 inline mr-1.5"></i>Bulk</a>
            </div>
            <?php endif; ?>

            <div class="flex items-center gap-3">
                <?php if ($currentUser): ?>
                    <div class="relative group">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-50 transition-all">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-400 to-lilac-400 flex items-center justify-center">
                                <span class="text-xs font-bold text-white"><?= strtoupper(substr($currentUser['username'], 0, 2)) ?></span>
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-slate-700"><?= htmlspecialchars($currentUser['username']) ?></span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                        </button>
                        <div class="dropdown-menu absolute right-0 top-full mt-2 w-52 bg-white rounded-xl py-2 shadow-elevated border border-slate-100">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs text-slate-400">Signed in as</p>
                                <p class="text-sm font-semibold text-slate-700 truncate"><?= htmlspecialchars($currentUser['email']) ?></p>
                            </div>
                            <a href="<?= url('/profile') ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-sm hover:bg-slate-50 text-slate-600 hover:text-slate-800 transition-colors"><i data-lucide="user" class="w-4 h-4"></i> Profile</a>
                            <a href="<?= url('/history') ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-sm hover:bg-slate-50 text-slate-600 hover:text-slate-800 transition-colors"><i data-lucide="clock" class="w-4 h-4"></i> My QR Codes</a>
                            <hr class="border-slate-100 my-1">
                            <a href="<?= url('/logout') ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-sm hover:bg-rose-50 text-rose-500 hover:text-rose-600 transition-colors"><i data-lucide="log-out" class="w-4 h-4"></i> Sign Out</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= url('/login') ?>" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Log in</a>
                    <a href="<?= url('/register') ?>" class="px-5 py-2.5 rounded-xl text-sm font-semibold btn-primary">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php if ($currentUser): ?>
<div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/90 backdrop-blur-xl border-t border-slate-200/60">
    <div class="flex items-center justify-around py-2 px-2">
        <a href="<?= url('/dashboard') ?>" class="flex flex-col items-center py-1.5 px-3 rounded-xl text-xs <?= $currentPage === 'dashboard' ? 'text-brand-600 font-semibold' : 'text-slate-400' ?>"><i data-lucide="layout-dashboard" class="w-5 h-5 mb-0.5"></i> Home</a>
        <a href="<?= url('/generate') ?>" class="flex flex-col items-center py-1.5 px-3 rounded-xl text-xs <?= $currentPage === 'generate' ? 'text-brand-600 font-semibold' : 'text-slate-400' ?>"><i data-lucide="plus-circle" class="w-5 h-5 mb-0.5"></i> Create</a>
        <a href="<?= url('/history') ?>" class="flex flex-col items-center py-1.5 px-3 rounded-xl text-xs <?= $currentPage === 'history' ? 'text-brand-600 font-semibold' : 'text-slate-400' ?>"><i data-lucide="clock" class="w-5 h-5 mb-0.5"></i> History</a>
        <a href="<?= url('/profile') ?>" class="flex flex-col items-center py-1.5 px-3 rounded-xl text-xs <?= $currentPage === 'profile' ? 'text-brand-600 font-semibold' : 'text-slate-400' ?>"><i data-lucide="user" class="w-5 h-5 mb-0.5"></i> Profile</a>
    </div>
</div>
<?php endif; ?>

<div class="pt-16 <?= $currentUser ? 'pb-20 md:pb-0' : '' ?>">
