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
    tailwind.config={theme:{extend:{colors:{brand:{50:'#FFF5F1',100:'#FFE8DF',200:'#FFD0BE',300:'#FFA98A',400:'#FF7750',500:'#FF5C35',600:'#F04020',700:'#CC2A0E'},neutral:{50:'#F9F9F8',100:'#F3F2F0',200:'#E8E7E3',300:'#D0CECC',400:'#A0A09C',500:'#737370',600:'#555552',700:'#3A3A38',800:'#262624',900:'#111110'}},fontFamily:{display:["'Playfair Display'",'Georgia','serif'],body:["'DM Sans'",'sans-serif'],mono:["'JetBrains Mono'",'monospace']}}}}
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600;1,700&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        :root{--orange:#FF5C35;--orange-light:#FFF3EE;--bg:#FFFFFF;--bg2:#F8F7F5;--bg3:#EFEDE9;--text:#111110;--text2:#55524E;--text3:#9E9B96;--border:#E8E7E3;--border2:#D0CECC;--r:12px}
        *{box-sizing:border-box}
        body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}
        h1,h2,h3,h4,h5{font-family:'Playfair Display',Georgia,serif}
        ::-webkit-scrollbar{width:5px;height:5px}::-webkit-scrollbar-track{background:var(--bg2)}::-webkit-scrollbar-thumb{background:var(--border2);border-radius:99px}
        .btn{display:inline-flex;align-items:center;gap:8px;font-family:'DM Sans',sans-serif;font-weight:600;font-size:14px;padding:10px 20px;border-radius:10px;border:none;cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap;line-height:1.2}
        .btn-p{background:var(--orange);color:#fff;box-shadow:0 1px 3px rgba(255,92,53,.3),0 4px 12px rgba(255,92,53,.18)}
        .btn-p:hover{background:#F04020;transform:translateY(-1px);box-shadow:0 2px 6px rgba(255,92,53,.35),0 6px 20px rgba(255,92,53,.22)}
        .btn-s{background:var(--bg);color:var(--text);border:1.5px solid var(--border2);box-shadow:0 1px 3px rgba(0,0,0,.04)}
        .btn-s:hover{background:var(--bg2);transform:translateY(-1px)}
        .btn-lg{padding:13px 26px;font-size:15px;border-radius:12px}
        .btn-sm{padding:7px 14px;font-size:13px;border-radius:8px}
        .field{width:100%;padding:11px 16px;background:var(--bg);color:var(--text);border:1.5px solid var(--border);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:14px;transition:all .15s}
        .field:focus{outline:none;border-color:var(--orange);box-shadow:0 0 0 3px rgba(255,92,53,.12)}
        .field::placeholder{color:var(--text3)}
        select.field{-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23A09E99' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:40px}
        textarea.field{resize:vertical;min-height:80px}
        .card{background:var(--bg);border:1px solid var(--border);border-radius:16px;transition:box-shadow .25s,transform .25s,border-color .25s}
        .card-hover:hover{box-shadow:0 4px 24px rgba(0,0,0,.07);transform:translateY(-2px);border-color:var(--border2)}
        .badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;font-family:'DM Sans',sans-serif}
        .badge-o{background:var(--orange-light);color:var(--orange)}
        .badge-g{background:var(--bg2);color:var(--text2);border:1px solid var(--border)}
        .badge-gr{background:#F0FDF4;color:#16A34A}
        .nav-link{display:flex;align-items:center;gap:6px;padding:7px 12px;border-radius:9px;font-size:14px;font-weight:500;color:var(--text2);text-decoration:none;transition:all .15s;font-family:'DM Sans',sans-serif}
        .nav-link:hover{color:var(--text);background:var(--bg2)}
        .nav-link.active{color:var(--text);background:var(--bg3);font-weight:600}
        .dropdown{position:relative}
        .dd{position:absolute;right:0;top:calc(100% + 8px);width:220px;background:var(--bg);border:1px solid var(--border);border-radius:14px;padding:6px;box-shadow:0 8px 32px rgba(0,0,0,.1);opacity:0;visibility:hidden;transform:translateY(8px) scale(.97);transition:all .2s;z-index:100}
        .dropdown:hover .dd,.dropdown:focus-within .dd{opacity:1;visibility:visible;transform:translateY(0) scale(1)}
        .dd-item{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:9px;font-size:14px;color:var(--text2);cursor:pointer;text-decoration:none;transition:all .12s;font-family:'DM Sans',sans-serif}
        .dd-item:hover{background:var(--bg2);color:var(--text)}
        .dd-item.danger:hover{background:#FFF1F1;color:#DC2626}
        .label{display:block;font-size:13px;font-weight:600;color:var(--text2);margin-bottom:7px;font-family:'DM Sans',sans-serif}
        .mn-item{display:flex;flex-direction:column;align-items:center;gap:3px;padding:8px 12px;border-radius:12px;font-size:10px;font-weight:700;letter-spacing:.02em;color:var(--text3);text-decoration:none;transition:all .15s;font-family:'DM Sans',sans-serif}
        .mn-item.active{color:var(--orange);background:var(--orange-light)}
        .mn-item:not(.active):hover{color:var(--text2);background:var(--bg2)}
        .text-grad{background:linear-gradient(135deg,var(--orange) 0%,#FF9E5E 60%,#FFBC8E 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .divider{height:1px;background:var(--border)}
        .alert{display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border-radius:12px;border-width:1px;border-style:solid;font-size:14px;font-family:'DM Sans',sans-serif}
        .alert-ok{background:#F0FDF4;border-color:#BBF7D0;color:#15803D}
        .alert-err{background:#FFF1F1;border-color:#FECACA;color:#DC2626}
        .section-tag{font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--orange);font-family:'DM Sans',sans-serif}
        input[type=color]{-webkit-appearance:none;border:1.5px solid var(--border);border-radius:10px;width:100%;height:44px;padding:3px;cursor:pointer}
        input[type=color]::-webkit-color-swatch-wrapper{padding:0}
        input[type=color]::-webkit-color-swatch{border:none;border-radius:7px}
        @keyframes fadeUp{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        @keyframes scaleIn{from{opacity:0;transform:scale(.96)}to{opacity:1;transform:scale(1)}}
        @keyframes floatY{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
        @keyframes marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}
        .a-up{animation:fadeUp .6s cubic-bezier(.22,1,.36,1) both}
        .a-in{animation:fadeIn .5s ease both}
        .a-sc{animation:scaleIn .4s cubic-bezier(.22,1,.36,1) both}
        .a-fl{animation:floatY 5s ease-in-out infinite}
        .d1{animation-delay:.06s}.d2{animation-delay:.13s}.d3{animation-delay:.20s}.d4{animation-delay:.27s}.d5{animation-delay:.34s}.d6{animation-delay:.42s}
        .bg-dots{background-image:radial-gradient(circle,rgba(0,0,0,.05) 1px,transparent 1px);background-size:28px 28px}
    </style>
</head>
<body>

<?php if($flash): ?>
<div id="fb" class="fixed top-4 right-4 z-[200] max-w-sm a-sc">
    <div class="alert <?= $flash['type']==='success'?'alert-ok':'alert-err' ?> shadow-xl">
        <i data-lucide="<?= $flash['type']==='success'?'check-circle-2':'alert-circle' ?>" class="w-5 h-5 flex-shrink-0"></i>
        <span class="font-medium"><?= htmlspecialchars($flash['message']) ?></span>
        <button onclick="document.getElementById('fb').remove()" class="ml-auto opacity-50 hover:opacity-100 transition-opacity"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
</div>
<script>setTimeout(()=>document.getElementById('fb')?.remove(),5000)</script>
<?php endif; ?>

<nav class="fixed top-0 left-0 right-0 z-50 h-16 bg-white/90 backdrop-blur-xl border-b border-neutral-200/70">
    <div class="max-w-7xl mx-auto px-4 sm:px-5 h-full flex items-center justify-between gap-4">
        <a href="<?= url('/') ?>" class="flex items-center gap-2.5 flex-shrink-0 group">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:var(--orange);box-shadow:0 2px 8px rgba(255,92,53,.35)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/></svg>
            </div>
            <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:18px;letter-spacing:-.2px;color:#111110">QR<span style="color:var(--orange)">Pro</span></span>
        </a>

        <?php if($currentUser): ?>
        <div class="hidden md:flex items-center gap-1">
            <a href="<?= url('/dashboard') ?>" class="nav-link <?= $currentPage==='dashboard'?'active':'' ?>"><i data-lucide="layout-dashboard" class="w-4 h-4"></i>Dashboard</a>
            <a href="<?= url('/generate') ?>" class="nav-link <?= $currentPage==='generate'?'active':'' ?>"><i data-lucide="sparkles" class="w-4 h-4"></i>Generate</a>
            <a href="<?= url('/history') ?>" class="nav-link <?= $currentPage==='history'?'active':'' ?>"><i data-lucide="history" class="w-4 h-4"></i>History</a>
            <a href="<?= url('/bulk') ?>" class="nav-link <?= $currentPage==='bulk'?'active':'' ?>"><i data-lucide="layers" class="w-4 h-4"></i>Bulk</a>
        </div>
        <?php endif; ?>

        <div class="flex items-center gap-2">
            <?php if($currentUser): ?>
            <a href="<?= url('/generate') ?>" class="btn btn-p btn-sm hidden sm:inline-flex"><i data-lucide="plus" class="w-3.5 h-3.5"></i>New QR</a>
            <div class="dropdown">
                <button class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-xl hover:bg-neutral-100 transition-colors">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm text-white" style="font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,var(--orange),#FF9E5E)"><?= strtoupper(substr($currentUser['username'],0,2)) ?></div>
                    <span class="hidden sm:block text-sm font-medium text-neutral-700"><?= htmlspecialchars($currentUser['username']) ?></span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-neutral-400"></i>
                </button>
                <div class="dd">
                    <div class="px-3 py-2.5 border-b border-neutral-100 mb-1">
                        <p class="text-xs text-neutral-400 font-medium">Signed in as</p>
                        <p class="text-sm font-semibold text-neutral-800 truncate mt-0.5"><?= htmlspecialchars($currentUser['email']) ?></p>
                    </div>
                    <a href="<?= url('/profile') ?>" class="dd-item"><i data-lucide="user" class="w-4 h-4"></i>My Profile</a>
                    <a href="<?= url('/history') ?>" class="dd-item"><i data-lucide="clock" class="w-4 h-4"></i>My QR Codes</a>
                    <a href="<?= url('/bulk') ?>" class="dd-item"><i data-lucide="layers" class="w-4 h-4"></i>Bulk Generate</a>
                    <div class="divider my-1.5 mx-2"></div>
                    <a href="<?= url('/logout') ?>" class="dd-item danger"><i data-lucide="log-out" class="w-4 h-4"></i>Sign Out</a>
                </div>
            </div>
            <?php else: ?>
            <a href="<?= url('/login') ?>" class="btn btn-s btn-sm">Log in</a>
            <a href="<?= url('/register') ?>" class="btn btn-p btn-sm">Get started</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php if($currentUser): ?>
<div class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-t border-neutral-200/80">
    <div class="flex items-center justify-around px-2 py-2">
        <a href="<?= url('/dashboard') ?>" class="mn-item <?= $currentPage==='dashboard'?'active':'' ?>"><i data-lucide="layout-dashboard" class="w-5 h-5"></i>Home</a>
        <a href="<?= url('/generate') ?>" class="mn-item <?= $currentPage==='generate'?'active':'' ?>"><i data-lucide="sparkles" class="w-5 h-5"></i>Create</a>
        <a href="<?= url('/history') ?>" class="mn-item <?= $currentPage==='history'?'active':'' ?>"><i data-lucide="clock" class="w-5 h-5"></i>History</a>
        <a href="<?= url('/bulk') ?>" class="mn-item <?= $currentPage==='bulk'?'active':'' ?>"><i data-lucide="layers" class="w-5 h-5"></i>Bulk</a>
        <a href="<?= url('/profile') ?>" class="mn-item <?= $currentPage==='profile'?'active':'' ?>"><i data-lucide="user" class="w-5 h-5"></i>Profile</a>
    </div>
</div>
<?php endif; ?>

<div class="pt-16 <?= $currentUser?'pb-24 md:pb-0':'' ?>">