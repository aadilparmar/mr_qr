<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
requireLogin();

$user = getCurrentUser();
$stats = getUserStats($user['id']);
$recentCodes = getUserQRCodes($user['id'], 8);
$types = getQRTypes();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-10 animate-fade-in-up">
        <div>
            <h1 class="font-display text-3xl font-bold mb-1 text-slate-900">
                Welcome back, <span class="gradient-text"><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></span>
            </h1>
            <p class="text-slate-500">Here's an overview of your QR code activity</p>
        </div>
        <a href="<?= url('/generate') ?>" class="mt-4 sm:mt-0 inline-flex items-center gap-2 px-5 py-3 rounded-xl font-display font-semibold btn-primary">
            <i data-lucide="plus" class="w-5 h-5"></i> New QR Code</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-soft card-hover animate-fade-in-up delay-100">
            <div class="flex items-center justify-between mb-3"><div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center"><i data-lucide="qr-code" class="w-5 h-5 text-brand-500"></i></div><span class="tag bg-brand-50 text-brand-600">TOTAL</span></div>
            <div class="font-display text-3xl font-bold text-slate-900"><?= number_format($stats['total_codes']) ?></div>
            <p class="text-sm text-slate-500 mt-1">QR Codes Created</p>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-soft card-hover animate-fade-in-up delay-200">
            <div class="flex items-center justify-between mb-3"><div class="w-10 h-10 rounded-xl bg-lilac-50 flex items-center justify-center"><i data-lucide="scan-line" class="w-5 h-5 text-lilac-500"></i></div><span class="tag bg-lilac-50 text-lilac-500">ACTIVITY</span></div>
            <div class="font-display text-3xl font-bold text-slate-900"><?= number_format($stats['total_scans']) ?></div>
            <p class="text-sm text-slate-500 mt-1">Total Scans</p>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-soft card-hover animate-fade-in-up delay-300">
            <div class="flex items-center justify-between mb-3"><div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center"><i data-lucide="folder" class="w-5 h-5 text-sky-500"></i></div><span class="tag bg-sky-50 text-sky-500">TYPES</span></div>
            <div class="font-display text-3xl font-bold text-slate-900"><?= count($stats['by_type']) ?></div>
            <p class="text-sm text-slate-500 mt-1">QR Types Used</p>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-soft card-hover animate-fade-in-up delay-400">
            <div class="flex items-center justify-between mb-3"><div class="w-10 h-10 rounded-xl bg-mint-50 flex items-center justify-center"><i data-lucide="crown" class="w-5 h-5 text-mint-500"></i></div><span class="tag bg-mint-50 text-mint-500">PLAN</span></div>
            <div class="font-display text-3xl font-bold text-slate-900 capitalize"><?= $user['plan'] ?></div>
            <p class="text-sm text-slate-500 mt-1">Current Plan</p>
        </div>
    </div>

    <div class="mb-10 animate-fade-in-up delay-500">
        <h2 class="font-display text-xl font-bold mb-5 text-slate-800">Quick Generate</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            <?php foreach (['url','wifi','vcard','email','phone','whatsapp','upi','text'] as $key): $t = $types[$key]; ?>
            <a href="<?= url('/generate?type=' . $key) ?>" class="bg-white rounded-xl p-4 text-center group card-hover border border-slate-100 shadow-soft">
                <div class="w-10 h-10 rounded-lg mx-auto mb-2 flex items-center justify-center transition-transform group-hover:scale-110" style="background: <?= $t['color'] ?>12;">
                    <i data-lucide="<?= $t['icon'] ?>" class="w-5 h-5" style="color: <?= $t['color'] ?>"></i></div>
                <span class="text-xs font-semibold text-slate-600"><?= $t['name'] ?></span></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="animate-fade-in-up delay-600">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-display text-xl font-bold text-slate-800">Recent QR Codes</h2>
            <?php if ($stats['total_codes'] > 0): ?>
            <a href="<?= url('/history') ?>" class="text-sm text-brand-500 hover:text-brand-600 font-semibold flex items-center gap-1">View all <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
            <?php endif; ?>
        </div>
        
        <?php if (empty($recentCodes)): ?>
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-100 shadow-soft">
            <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-4"><i data-lucide="qr-code" class="w-8 h-8 text-slate-300"></i></div>
            <h3 class="font-display text-lg font-semibold mb-2 text-slate-700">No QR codes yet</h3>
            <p class="text-slate-500 text-sm mb-6">Create your first QR code to get started</p>
            <a href="<?= url('/generate') ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold btn-primary"><i data-lucide="plus" class="w-4 h-4"></i> Create QR Code</a>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($recentCodes as $code): $typeInfo = $types[$code['type']] ?? ['name'=>$code['type'],'icon'=>'qr-code','color'=>'#666']; ?>
            <div class="bg-white rounded-2xl p-5 card-hover border border-slate-100 shadow-soft">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: <?= $typeInfo['color'] ?>12;"><i data-lucide="<?= $typeInfo['icon'] ?>" class="w-4 h-4" style="color: <?= $typeInfo['color'] ?>"></i></div>
                    <span class="text-xs text-slate-400 font-medium"><?= date('M j', strtotime($code['created_at'])) ?></span></div>
                <h4 class="font-semibold text-sm truncate mb-1 text-slate-700"><?= htmlspecialchars($code['title'] ?: $typeInfo['name']) ?></h4>
                <p class="text-xs text-slate-400 truncate mb-3"><?= htmlspecialchars(substr($code['content'], 0, 50)) ?></p>
                <div class="flex items-center justify-between">
                    <span class="tag" style="background: <?= $typeInfo['color'] ?>0D; color: <?= $typeInfo['color'] ?>"><?= $code['type'] ?></span>
                    <span class="text-xs text-slate-400 flex items-center gap-1"><i data-lucide="scan-line" class="w-3 h-3"></i> <?= $code['scans'] ?></span></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
