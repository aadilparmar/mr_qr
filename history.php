<?php
$pageTitle = 'My QR Codes';
require_once __DIR__ . '/includes/header.php';
requireLogin();
$types = getQRTypes();
$filterType = $_GET['type'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleted = deleteQRCode((int)$_POST['delete_id'], $currentUser['id']);
    if ($deleted) setFlash('success', 'QR code deleted');
    header('Location: ' . url('/history') . ($filterType ? "?type={$filterType}" : ''));
    exit;
}
$codes = getUserQRCodes($currentUser['id'], $perPage, $offset, $filterType);
$db = getDB();
$countSql = "SELECT COUNT(*) as total FROM qr_codes WHERE user_id = ?";
$countParams = [$currentUser['id']];
if ($filterType) { $countSql .= " AND type = ?"; $countParams[] = $filterType; }
$stmt = $db->prepare($countSql); $stmt->execute($countParams);
$totalCodes = $stmt->fetch()['total'];
$totalPages = ceil($totalCodes / $perPage);
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4 animate-fade-in-up">
        <div><h1 class="font-display text-3xl font-bold mb-1 text-slate-900">My QR Codes</h1><p class="text-slate-500"><?= number_format($totalCodes) ?> QR codes total</p></div>
        <a href="<?= url('/generate') ?>" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl font-display font-semibold btn-primary"><i data-lucide="plus" class="w-5 h-5"></i> New QR Code</a>
    </div>
    <div class="flex flex-wrap gap-2 mb-8 animate-fade-in-up delay-100">
        <a href="<?= url('/history') ?>" class="px-3 py-1.5 rounded-xl text-sm font-medium transition-all <?= !$filterType ? 'bg-brand-50 text-brand-700 ring-1 ring-brand-200' : 'text-slate-500 hover:bg-slate-50' ?>">All</a>
        <?php foreach ($types as $key => $type): if ($key === 'bulk') continue; ?>
        <a href="<?= url('/history?type=' . $key) ?>" class="px-3 py-1.5 rounded-xl text-sm font-medium transition-all <?= $filterType === $key ? 'bg-brand-50 text-brand-700 ring-1 ring-brand-200' : 'text-slate-500 hover:bg-slate-50' ?>"><?= $type['name'] ?></a>
        <?php endforeach; ?>
    </div>
    <?php if (empty($codes)): ?>
    <div class="bg-white rounded-2xl p-16 text-center border border-slate-100 shadow-soft">
        <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-4"><i data-lucide="search" class="w-8 h-8 text-slate-300"></i></div>
        <h3 class="font-display text-lg font-semibold mb-2 text-slate-700">No QR codes found</h3>
        <p class="text-slate-400 text-sm mb-6"><?= $filterType ? 'Try a different filter or create' : 'Create' ?> your first QR code.</p>
        <a href="<?= url('/generate') ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold btn-primary"><i data-lucide="plus" class="w-4 h-4"></i> Create QR Code</a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
        <?php foreach ($codes as $code): $typeInfo = $types[$code['type']] ?? ['name'=>$code['type'],'icon'=>'qr-code','color'=>'#666']; $settings = json_decode($code['settings'], true) ?: []; ?>
        <div class="bg-white rounded-2xl p-5 card-hover border border-slate-100 shadow-soft">
            <div class="bg-slate-50 rounded-xl p-3 mb-4 text-center"><div class="qr-mini" data-content="<?= htmlspecialchars($code['content']) ?>" data-fg="<?= $settings['fg_color'] ?? '#000000' ?>" data-bg="<?= $settings['bg_color'] ?? '#ffffff' ?>"></div></div>
            <div class="flex items-start gap-3"><div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center" style="background: <?= $typeInfo['color'] ?>12;"><i data-lucide="<?= $typeInfo['icon'] ?>" class="w-4 h-4" style="color: <?= $typeInfo['color'] ?>"></i></div><div class="flex-1 min-w-0"><h4 class="font-semibold text-sm truncate text-slate-700"><?= htmlspecialchars($code['title'] ?: $typeInfo['name']) ?></h4><p class="text-xs text-slate-400 truncate"><?= htmlspecialchars(substr($code['content'], 0, 40)) ?></p></div></div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
                <span class="text-xs text-slate-400"><?= date('M j, Y', strtotime($code['created_at'])) ?></span>
                <div class="flex items-center gap-2">
                    <a href="<?= url('/generate?type=' . $code['type']) ?>" class="text-xs text-slate-400 hover:text-brand-500 transition-colors" title="Generate similar"><i data-lucide="copy" class="w-3.5 h-3.5"></i></a>
                    <form method="POST" class="inline" onsubmit="return confirm('Delete this QR code?')"><input type="hidden" name="delete_id" value="<?= $code['id'] ?>"><button type="submit" class="text-xs text-slate-400 hover:text-rose-500 transition-colors" title="Delete"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button></form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-center gap-2">
        <?php if ($page > 1): ?><a href="?page=<?= $page-1 ?><?= $filterType?"&type={$filterType}":'' ?>" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-sm hover:bg-slate-50 text-slate-600 font-medium"><i data-lucide="chevron-left" class="w-4 h-4 inline"></i> Prev</a><?php endif; ?>
        <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?>
        <a href="?page=<?= $i ?><?= $filterType?"&type={$filterType}":'' ?>" class="px-3.5 py-2 rounded-xl text-sm font-medium transition-all <?= $i===$page?'bg-brand-500 text-white shadow-sm':'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?><a href="?page=<?= $page+1 ?><?= $filterType?"&type={$filterType}":'' ?>" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-sm hover:bg-slate-50 text-slate-600 font-medium">Next <i data-lucide="chevron-right" class="w-4 h-4 inline"></i></a><?php endif; ?>
    </div>
    <?php endif; endif; ?>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.qr-mini').forEach(el => {
        const c=el.dataset.content, fg=el.dataset.fg||'#000', bg=el.dataset.bg||'#fff';
        if(c) new QRCode(el,{text:c,width:120,height:120,colorDark:fg,colorLight:bg,correctLevel:QRCode.CorrectLevel.M});
    });
    lucide.createIcons();
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
