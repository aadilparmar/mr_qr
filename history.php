<?php
// ── Auth + POST handler BEFORE any HTML output ────────────────────────────────
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$currentUser = getCurrentUser();
$types       = getQRTypes();
$filterType  = $_GET['type'] ?? '';
$page        = max(1, (int)($_GET['page'] ?? 1));
$perPage     = 12;
$offset      = ($page - 1) * $perPage;

// ── Handle delete ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleted = deleteQRCode((int)$_POST['delete_id'], $currentUser['id']);
    if ($deleted) setFlash('success', 'QR code deleted');
    header('Location: ' . url('/history') . ($filterType ? "?type={$filterType}" : ''));
    exit;
}

// ── Handle favorite toggle ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favorite_id'])) {
    toggleFavorite((int)$_POST['favorite_id'], $currentUser['id']);
    $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ' . url('/history') . $qs);
    exit;
}

$filterFavorites = isset($_GET['favorites']);
// ── Build custom query to support favorites filter ─────────────────────────
$db         = getDB();
$sql        = "SELECT * FROM qr_codes WHERE user_id = ?";
$countSql   = "SELECT COUNT(*) as total FROM qr_codes WHERE user_id = ?";
$params      = [$currentUser['id']];
$countParams = [$currentUser['id']];
if ($filterType) {
    $sql .= " AND type = ?"; $params[] = $filterType;
    $countSql .= " AND type = ?"; $countParams[] = $filterType;
}
if ($filterFavorites) {
    $sql .= " AND is_favorite = 1";
    $countSql .= " AND is_favorite = 1";
}
$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$stmt = $db->prepare($sql);
$stmt->execute($params);
$codes = $stmt->fetchAll();

$stmt = $db->prepare($countSql);
$stmt->execute($countParams);
$totalCodes = $stmt->fetch()['total'];
$totalPages = ceil($totalCodes / $perPage);

// ── Now safe to output HTML ────────────────────────────────────────────────────
$pageTitle = 'My QR Codes';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-5 py-10">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 a-up">
        <div>
            <p class="section-tag mb-1">Collection</p>
            <h1 class="font-display font-extrabold text-3xl text-neutral-900">My QR Codes</h1>
            <p class="text-neutral-500 mt-1"><?= number_format($totalCodes) ?> QR codes total</p>
        </div>
        <a href="<?= url('/generate') ?>" class="btn btn-p self-start sm:self-auto">
            <i data-lucide="plus" class="w-4 h-4"></i> New QR Code
        </a>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-2 mb-8 a-up d1">
        <a href="<?= url('/history') ?>" class="btn btn-sm <?= !$filterType && !$filterFavorites ? 'btn-p' : 'btn-s' ?>">All</a>
        <a href="<?= url('/history?favorites=1') ?>" class="btn btn-sm <?= $filterFavorites ? 'btn-p' : 'btn-s' ?>">
            <i data-lucide="star" class="w-3.5 h-3.5"></i> Favorites
        </a>
        <?php foreach ($types as $key => $type): if ($key === 'bulk') continue; ?>
        <a href="<?= url('/history?type=' . $key) ?>" class="btn btn-sm <?= $filterType === $key ? 'btn-p' : 'btn-s' ?>">
            <?= $type['name'] ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Search & Export -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6 a-up d1">
        <div class="relative flex-1">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 pointer-events-none"></i>
            <input type="text" id="qr-search" placeholder="Search by title, content, or type..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-neutral-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all"
                   style="background:var(--bg2)">
        </div>
        <button onclick="exportCSV()" class="btn btn-s btn-sm self-start sm:self-auto">
            <i data-lucide="download" class="w-4 h-4"></i> Export CSV
        </button>
    </div>

    <?php if (empty($codes)): ?>
    <div class="card p-16 text-center a-up d2" style="border-radius:20px;border-style:dashed">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:var(--bg2)">
            <i data-lucide="search" class="w-8 h-8 text-neutral-300"></i>
        </div>
        <h3 class="font-display font-bold text-lg mb-2 text-neutral-700">No QR codes found</h3>
        <p class="text-neutral-400 text-sm mb-6"><?= $filterType ? 'Try a different filter or create' : 'Create' ?> your first QR code.</p>
        <a href="<?= url('/generate') ?>" class="btn btn-p"><i data-lucide="plus" class="w-4 h-4"></i> Create QR Code</a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8 a-up d2">
        <?php foreach ($codes as $code):
            $typeInfo = $types[$code['type']] ?? ['name'=>$code['type'],'icon'=>'qr-code','color'=>'#999'];
            $settings = json_decode($code['settings'], true) ?: []; ?>
        <div class="card group overflow-hidden qr-card"
             style="border-radius:16px"
             data-title="<?= htmlspecialchars($code['title'] ?: $typeInfo['name']) ?>"
             data-content="<?= htmlspecialchars($code['content']) ?>"
             data-type="<?= htmlspecialchars($code['type']) ?>"
             data-created="<?= htmlspecialchars($code['created_at']) ?>"
             data-favorite="<?= $code['is_favorite'] ?? 0 ?>">
            <!-- QR preview -->
            <div class="p-4 flex items-center justify-center" style="background:var(--bg2);min-height:120px">
                <div class="qr-mini"
                     data-content="<?= htmlspecialchars($code['content']) ?>"
                     data-fg="<?= $settings['fg_color'] ?? '#000000' ?>"
                     data-bg="<?= $settings['bg_color'] ?? '#ffffff' ?>"></div>
            </div>
            <!-- Info -->
            <div class="p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-6 h-6 rounded-lg flex-shrink-0 flex items-center justify-center" style="background:<?= $typeInfo['color'] ?>15">
                        <i data-lucide="<?= $typeInfo['icon'] ?>" class="w-3.5 h-3.5" style="color:<?= $typeInfo['color'] ?>"></i>
                    </div>
                    <span class="text-xs font-bold text-neutral-500 uppercase tracking-wide"><?= $code['type'] ?></span>
                </div>
                <h4 class="font-bold text-sm text-neutral-800 truncate mb-1"><?= htmlspecialchars($code['title'] ?: $typeInfo['name']) ?></h4>
                <p class="text-xs text-neutral-400 truncate font-medium"><?= htmlspecialchars(substr($code['content'], 0, 38)) ?></p>

                <div class="flex items-center justify-between mt-3 pt-3 border-t border-neutral-100">
                    <span class="text-xs text-neutral-400 font-medium"><?= date('M j', strtotime($code['created_at'])) ?></span>
                    <div class="flex items-center gap-1.5">
                        <!-- Favorite toggle -->
                        <form method="POST" class="inline">
                            <input type="hidden" name="favorite_id" value="<?= $code['id'] ?>">
                            <button type="submit"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center transition-all <?= ($code['is_favorite'] ?? 0) ? 'text-yellow-500 hover:bg-yellow-50' : 'text-neutral-400 hover:text-yellow-500 hover:bg-yellow-50' ?>"
                                    title="<?= ($code['is_favorite'] ?? 0) ? 'Remove from favorites' : 'Add to favorites' ?>">
                                <i data-lucide="<?= ($code['is_favorite'] ?? 0) ? 'star' : 'star' ?>" class="w-3.5 h-3.5" <?= ($code['is_favorite'] ?? 0) ? 'style="fill:currentColor"' : '' ?>></i>
                            </button>
                        </form>
                        <!-- Copy content -->
                        <button onclick="copyContent(this)" data-copy="<?= htmlspecialchars($code['content']) ?>"
                                class="w-7 h-7 rounded-lg flex items-center justify-center text-neutral-400 hover:text-green-500 hover:bg-green-50 transition-all"
                                title="Copy content">
                            <i data-lucide="clipboard-copy" class="w-3.5 h-3.5"></i>
                        </button>
                        <a href="<?= url('/generate?type=' . $code['type']) ?>"
                           class="w-7 h-7 rounded-lg flex items-center justify-center text-neutral-400 hover:text-blue-500 hover:bg-blue-50 transition-all"
                           title="Generate similar">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        </a>
                        <form method="POST" class="inline" onsubmit="return confirm('Delete this QR code?')">
                            <input type="hidden" name="delete_id" value="<?= $code['id'] ?>">
                            <button type="submit"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-neutral-400 hover:text-red-500 hover:bg-red-50 transition-all"
                                    title="Delete">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-center gap-2 a-up d3">
        <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?><?= $filterType ? "&type={$filterType}" : '' ?><?= $filterFavorites ? '&favorites=1' : '' ?>" class="btn btn-s btn-sm">
            <i data-lucide="chevron-left" class="w-4 h-4"></i> Prev
        </a>
        <?php endif; ?>
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
        <a href="?page=<?= $i ?><?= $filterType ? "&type={$filterType}" : '' ?><?= $filterFavorites ? '&favorites=1' : '' ?>"
           class="btn btn-sm <?= $i === $page ? 'btn-p' : 'btn-s' ?>">
            <?= $i ?>
        </a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?><?= $filterType ? "&type={$filterType}" : '' ?><?= $filterFavorites ? '&favorites=1' : '' ?>" class="btn btn-s btn-sm">
            Next <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.qr-mini').forEach(el => {
        const c = el.dataset.content, fg = el.dataset.fg || '#000', bg = el.dataset.bg || '#fff';
        if (c) new QRCode(el, {text:c, width:100, height:100, colorDark:fg, colorLight:bg, correctLevel:QRCode.CorrectLevel.M});
    });
    lucide.createIcons();

    // ── Live search ────────────────────────────────────────────────────────
    const searchInput = document.getElementById('qr-search');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.qr-card').forEach(card => {
                const title   = (card.dataset.title   || '').toLowerCase();
                const content = (card.dataset.content || '').toLowerCase();
                const type    = (card.dataset.type    || '').toLowerCase();
                const match   = !q || title.includes(q) || content.includes(q) || type.includes(q);
                card.style.display = match ? '' : 'none';
            });
        });
    }
});

// ── Copy content to clipboard ──────────────────────────────────────────────
function copyContent(btn) {
    const text = btn.dataset.copy;
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
        const icon = btn.querySelector('[data-lucide]');
        if (icon) {
            icon.setAttribute('data-lucide', 'check');
            lucide.createIcons();
            setTimeout(() => {
                icon.setAttribute('data-lucide', 'clipboard-copy');
                lucide.createIcons();
            }, 1500);
        }
    }).catch(() => {
        // Fallback for older browsers
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    });
}

// ── Export CSV ──────────────────────────────────────────────────────────────
function exportCSV() {
    const cards = document.querySelectorAll('.qr-card');
    const rows = [['Title', 'Type', 'Content', 'Created At']];
    cards.forEach(card => {
        if (card.style.display === 'none') return; // skip hidden (filtered out)
        rows.push([
            '"' + (card.dataset.title   || '').replace(/"/g, '""') + '"',
            '"' + (card.dataset.type    || '').replace(/"/g, '""') + '"',
            '"' + (card.dataset.content || '').replace(/"/g, '""') + '"',
            '"' + (card.dataset.created || '').replace(/"/g, '""') + '"'
        ]);
    });
    if (rows.length <= 1) { alert('No QR codes to export.'); return; }
    const csv  = rows.map(r => r.join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href  = URL.createObjectURL(blob);
    link.download = 'qr_codes_export.csv';
    link.click();
    URL.revokeObjectURL(link.href);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
