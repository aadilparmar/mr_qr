<?php
$pageTitle = 'Professional QR Code Generator';
require_once __DIR__ . '/includes/header.php';
$types = getQRTypes();
?>

<style>
/* ── Extra landing-only styles ── */
.hero-word {
    display:inline-block;
    transition: transform .4s cubic-bezier(.22,1,.36,1), color .3s;
}
.hero-word:hover { transform: skewX(-4deg) scale(1.04); color: var(--orange); }

/* Cursor glow */
#cursor-glow {
    pointer-events:none; position:fixed; z-index:0;
    width:500px; height:500px; border-radius:50%;
    background: radial-gradient(circle, rgba(255,92,53,.06) 0%, transparent 70%);
    transform: translate(-50%,-50%);
    transition: left .08s ease, top .08s ease;
}

/* Tilt card */
.tilt-card { transform-style: preserve-3d; transition: transform .15s ease; }

/* Scroll reveal */
.reveal { opacity:0; transform:translateY(32px); transition: opacity .7s cubic-bezier(.22,1,.36,1), transform .7s cubic-bezier(.22,1,.36,1); }
.reveal.visible { opacity:1; transform:translateY(0); }
.reveal.delay-1 { transition-delay:.1s; }
.reveal.delay-2 { transition-delay:.2s; }
.reveal.delay-3 { transition-delay:.3s; }
.reveal.delay-4 { transition-delay:.4s; }
.reveal.delay-5 { transition-delay:.55s; }

/* Marquee */
@keyframes marquee { from{transform:translateX(0)} to{transform:translateX(-50%)} }
@keyframes marquee-r { from{transform:translateX(-50%)} to{transform:translateX(0)} }

/* Number counter */
.counter { font-family:'Playfair Display',serif; }

/* Magnetic button */
.magnetic { transition: transform .3s cubic-bezier(.22,1,.36,1); display:inline-flex; }

/* Type card hover */
.type-card {
    position:relative; overflow:hidden;
    transition: transform .3s cubic-bezier(.22,1,.36,1), box-shadow .3s, border-color .3s;
}
.type-card::before {
    content:''; position:absolute; inset:0; opacity:0;
    transition: opacity .3s;
}
.type-card:hover { transform:translateY(-4px) scale(1.02); }
.type-card:hover::before { opacity:1; }

/* Live demo */
#demo-canvas-wrap canvas { border-radius:8px; }

/* Pulse ring */
@keyframes pulse-ring {
    0% { transform:scale(1); opacity:.4; }
    100% { transform:scale(1.6); opacity:0; }
}
.pulse-ring {
    position:absolute; inset:0; border-radius:inherit;
    border:2px solid var(--orange);
    animation: pulse-ring 2s ease-out infinite;
}

/* Animated underline */
.fancy-underline {
    position:relative; display:inline-block;
}
.fancy-underline::after {
    content:''; position:absolute; left:0; bottom:-4px;
    width:100%; height:3px; border-radius:99px;
    background: linear-gradient(90deg, var(--orange), #FF9E5E);
    transform: scaleX(0); transform-origin:left;
    transition: transform .5s cubic-bezier(.22,1,.36,1);
}
.fancy-underline.active::after, .fancy-underline:hover::after { transform:scaleX(1); }

/* Floating badge */
@keyframes float-badge {
    0%,100% { transform:translateY(0) rotate(0deg); }
    50% { transform:translateY(-8px) rotate(1deg); }
}
.float-badge { animation: float-badge 4s ease-in-out infinite; }
.float-badge-2 { animation: float-badge 5.5s ease-in-out infinite; animation-delay:1.5s; }
.float-badge-3 { animation: float-badge 4.5s ease-in-out infinite; animation-delay:0.8s; }

/* Progress bar animated */
@keyframes progress-fill { from{width:0} to{width:var(--w)} }

/* Stagger grid */
.type-grid-item { opacity:0; transform:translateY(20px) scale(.97); transition: opacity .5s, transform .5s; }
.type-grid-item.in { opacity:1; transform:translateY(0) scale(1); }

/* Feature icon spin on hover */
.feat-icon { transition: transform .5s cubic-bezier(.22,1,.36,1); }
.feat-card:hover .feat-icon { transform:rotate(8deg) scale(1.15); }

/* Glowing border on active tab */
.qr-tab.active-tab {
    background:var(--orange);
    color:#fff;
    box-shadow:0 4px 16px rgba(255,92,53,.35);
}
</style>

<!-- Cursor glow (desktop only) -->
<div id="cursor-glow" class="hidden md:block"></div>

<!-- ──────────────────────────────────────
     HERO
────────────────────────────────────────-->
<section class="relative overflow-hidden" style="background:#FDFCFA;min-height:92vh;display:flex;align-items:center">

    <!-- Animated bg blobs -->
    <div id="blob1" class="absolute w-[600px] h-[600px] rounded-full pointer-events-none" style="background:radial-gradient(circle,rgba(255,92,53,.10) 0%,transparent 70%);top:-120px;right:-100px;transition:transform .8s ease"></div>
    <div id="blob2" class="absolute w-[400px] h-[400px] rounded-full pointer-events-none" style="background:radial-gradient(circle,rgba(255,188,80,.08) 0%,transparent 70%);bottom:-80px;left:-80px"></div>

    <!-- Dot grid -->
    <div class="absolute inset-0 bg-dots opacity-60 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-20 md:py-0 w-full relative z-10">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-10 items-center">

            <!-- Left -->
            <div>
                <!-- Pill badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border mb-8 a-up" style="border-color:rgba(255,92,53,.25);background:var(--orange-light)">
                    <span class="w-1.5 h-1.5 rounded-full" style="background:var(--orange)"></span>
                    <span class="text-xs font-bold tracking-wide uppercase" style="color:var(--orange);font-family:'DM Sans',sans-serif">24+ types · Bulk · Free Forever</span>
                </div>

                <!-- Headline — Playfair, normal size, elegant -->
                <h1 class="font-display leading-[1.08] tracking-tight text-neutral-900 mb-6 a-up d1" style="font-size:clamp(2.4rem,5vw,3.8rem)">
                    <span class="hero-word">Create</span> <span class="hero-word text-grad italic">beautiful</span><br>
                    <span class="hero-word">QR codes</span> that<br>
                    <span class="hero-word fancy-underline active">work harder</span>
                </h1>

                <p class="text-neutral-500 max-w-md leading-relaxed mb-9 a-up d2" style="font-size:16px">
                    Create URL, WiFi, vCard, UPI, WhatsApp &amp; 20+ more QR types. Fully customisable. Export as PNG, SVG, or PDF. Always free.
                </p>

                <!-- CTAs -->
                <div class="flex flex-wrap gap-3 mb-10 a-up d3">
                    <?php if($currentUser): ?>
                    <a href="<?= url('/generate') ?>" class="btn btn-p btn-lg magnetic" id="cta-main">
                        <i data-lucide="sparkles" class="w-5 h-5"></i> Start Generating
                    </a>
                    <a href="<?= url('/dashboard') ?>" class="btn btn-s btn-lg">My Dashboard</a>
                    <?php else: ?>
                    <a href="<?= url('/register') ?>" class="btn btn-p btn-lg magnetic" id="cta-main">
                        <i data-lucide="zap" class="w-5 h-5"></i> Get Started Free
                    </a>
                    <a href="<?= url('/login') ?>" class="btn btn-s btn-lg">Log In</a>
                    <?php endif; ?>
                </div>

                <!-- Trust row -->
                <div class="flex flex-wrap gap-5 a-up d4">
                    <?php foreach([['check','No watermarks','green'],['shield','No sign-up required','blue'],['infinity','Unlimited generations','orange']] as [$ic,$txt,$col]): ?>
                    <div class="flex items-center gap-2 text-sm text-neutral-500" style="font-family:'DM Sans',sans-serif">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:<?= $col==='orange'?'var(--orange-light)':($col==='green'?'#F0FDF4':'#EFF6FF') ?>">
                            <i data-lucide="<?= $ic ?>" class="w-3.5 h-3.5" style="color:<?= $col==='orange'?'var(--orange)':($col==='green'?'#22C55E':'#3B82F6') ?>"></i>
                        </div>
                        <?= $txt ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Interactive live QR demo -->
            <div class="flex justify-center lg:justify-end a-up d3">
                <div class="relative" style="width:340px">

                    <!-- Main card with tilt -->
                    <div id="hero-card" class="tilt-card card p-7 shadow-xl" style="border-radius:24px;background:white">

                        <!-- Tab switcher -->
                        <div class="flex gap-1 p-1 rounded-xl mb-6" style="background:var(--bg2)">
                            <?php $demoTabs = ['url'=>['URL','link'],
                                'wifi'=>['WiFi','wifi'],
                                'whatsapp'=>['WhatsApp','message-circle'],
                                'text'=>['Text','type']]; ?>
                            <?php foreach($demoTabs as $k=>[$label,$icon]): ?>
                            <button onclick="switchDemoTab('<?= $k ?>')" data-tab="<?= $k ?>"
                                class="qr-tab flex-1 flex items-center justify-center gap-1 py-2 px-1 rounded-lg text-xs font-bold transition-all text-neutral-500"
                                style="font-family:'DM Sans',sans-serif">
                                <i data-lucide="<?= $icon ?>" class="w-3 h-3"></i>
                                <span class="hidden sm:inline"><?= $label ?></span>
                            </button>
                            <?php endforeach; ?>
                        </div>

                        <!-- QR Display -->
                        <div class="relative flex items-center justify-center mb-6" style="height:180px">
                            <div class="relative">
                                <div class="pulse-ring" style="animation-delay:0s"></div>
                                <div class="pulse-ring" style="animation-delay:1s"></div>
                                <div id="demo-canvas-wrap" class="relative z-10 p-3 rounded-2xl" style="background:white;box-shadow:0 4px 24px rgba(0,0,0,.08)">
                                    <div id="live-qr" style="width:140px;height:140px"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Live input -->
                        <div id="demo-input-wrap">
                            <p class="label mb-2">Enter URL to preview →</p>
                            <input id="demo-input" type="text" class="field text-sm" placeholder="https://yourwebsite.com" value="https://qrpro.app">
                        </div>

                        <!-- Color pickers -->
                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div>
                                <p class="label mb-1.5">Foreground</p>
                                <input type="color" id="demo-fg" value="#111110" style="height:36px">
                            </div>
                            <div>
                                <p class="label mb-1.5">Background</p>
                                <input type="color" id="demo-bg" value="#ffffff" style="height:36px">
                            </div>
                        </div>
                    </div>

                    <!-- Floating chips -->
                    <div class="float-badge absolute -top-4 -right-5 card px-3 py-2 shadow-lg" style="border-radius:99px;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;white-space:nowrap">
                        <span style="color:var(--orange)">✦</span> PNG · SVG · PDF
                    </div>
                    <div class="float-badge-2 absolute -bottom-4 -left-5 card px-3 py-2 shadow-lg" style="border-radius:99px;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;white-space:nowrap">
                        🚀 100 QRs in bulk
                    </div>
                    <div class="float-badge-3 absolute top-1/2 -right-10 card px-3 py-2 shadow-lg" style="border-radius:99px;font-size:12px;font-weight:700;font-family:'DM Sans',sans-serif;white-space:nowrap">
                        ✓ Free forever
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── ANIMATED COUNTER STATS ── -->
<section class="border-y border-neutral-200 py-10 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <?php foreach([['24+','QR Code Types','#FF5C35'],['∞','Free Generations','#111110'],['100','Bulk at Once','#111110'],['3','Export Formats','#111110']] as [$num,$label,$color]): ?>
            <div class="reveal">
                <p class="counter font-display font-bold" style="font-size:clamp(2rem,5vw,3.2rem);color:<?= $color ?>;line-height:1"><?= $num ?></p>
                <p class="text-sm text-neutral-500 font-medium mt-2" style="font-family:'DM Sans',sans-serif"><?= $label ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── MARQUEE TICKER ── -->
<div class="overflow-hidden py-4 border-b border-neutral-100" style="background:var(--bg2)">
    <div style="display:flex;animation:marquee 25s linear infinite;width:max-content">
        <?php $tkeys=array_filter(array_keys($types),fn($k)=>$k!=='bulk'); $rep=array_merge($tkeys,$tkeys,$tkeys,$tkeys); foreach($rep as $k): $t=$types[$k]; ?>
        <span class="flex items-center gap-2 px-5 text-sm font-semibold text-neutral-400 flex-shrink-0" style="font-family:'DM Sans',sans-serif">
            <i data-lucide="<?= $t['icon'] ?>" class="w-4 h-4 flex-shrink-0" style="color:<?= $t['color'] ?>"></i><?= $t['name'] ?>
        </span>
        <span class="text-neutral-200 text-lg self-center flex-shrink-0">·</span>
        <?php endforeach; ?>
    </div>
</div>

<!-- ── QR TYPES INTERACTIVE GRID ── -->
<section class="py-24" id="types-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14 reveal">
            <p class="section-tag mb-3">All Types Supported</p>
            <h2 class="font-display text-neutral-900 mb-4" style="font-size:clamp(1.8rem,4vw,2.8rem)">Every QR code<br><em>you'll ever need</em></h2>
            <p class="text-neutral-500 max-w-xl mx-auto" style="font-family:'DM Sans',sans-serif;font-size:16px">From simple URLs to complex vCards, WiFi networks to UPI payments — all covered.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3" id="types-grid">
            <?php foreach($types as $key=>$type): if($key==='bulk') continue; ?>
            <a href="<?= url('/generate?type='.$key) ?>" class="type-card card group p-4 text-center type-grid-item" style="border-radius:14px;--hover-color:<?= $type['color'] ?>">
                <div class="w-11 h-11 rounded-xl mx-auto mb-3 flex items-center justify-center" style="background:<?= $type['color'] ?>15;transition:all .3s">
                    <i data-lucide="<?= $type['icon'] ?>" class="w-5 h-5" style="color:<?= $type['color'] ?>"></i>
                </div>
                <h3 class="text-sm font-bold text-neutral-700 mb-1 leading-snug" style="font-family:'DM Sans',sans-serif"><?= $type['name'] ?></h3>
                <p class="text-xs text-neutral-400 leading-snug hidden sm:block" style="font-family:'DM Sans',sans-serif"><?= $type['desc'] ?></p>
                <!-- Hover accent bar -->
                <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-xl scale-x-0 group-hover:scale-x-100 transition-transform duration-300" style="background:<?= $type['color'] ?>"></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── REVERSE MARQUEE ── -->
<div class="overflow-hidden py-4 border-y border-neutral-100" style="background:var(--bg2)">
    <div style="display:flex;animation:marquee-r 30s linear infinite;width:max-content">
        <?php $rep2=array_merge(array_reverse($tkeys),$tkeys,array_reverse($tkeys)); foreach($rep2 as $k): $t=$types[$k]; ?>
        <span class="flex items-center gap-2 px-5 text-sm font-semibold text-neutral-400 flex-shrink-0" style="font-family:'DM Sans',sans-serif">
            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:<?= $t['color'] ?>"></span><?= $t['name'] ?>
        </span>
        <span class="text-neutral-200 text-lg self-center flex-shrink-0">·</span>
        <?php endforeach; ?>
    </div>
</div>

<!-- ── FEATURES ── -->
<section class="py-24" style="background:var(--bg2)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14 reveal">
            <p class="section-tag mb-3">Features</p>
            <h2 class="font-display text-neutral-900" style="font-size:clamp(1.8rem,4vw,2.8rem)">Built for professionals,<br><em>free for everyone</em></h2>
        </div>

        <div class="grid md:grid-cols-3 gap-5">
            <?php foreach([
                ['layers','Bulk Generation','#FF5C35','var(--orange-light)','Generate up to 100 QR codes at once. Upload a CSV or enter data manually. Download everything as a ZIP archive.'],
                ['palette','Full Customization','#3B82F6','#EFF6FF','Customise foreground & background colors, error correction levels, and export size. Make it match your brand.'],
                ['file-down','PDF & Image Export','#22C55E','#F0FDF4','Export as PNG, SVG, or embedded in PDF. Perfect for print materials, business cards, menus, and packaging.'],
            ] as [$icon,$title,$color,$bg,$desc]): ?>
            <div class="card feat-card p-8 reveal card-hover" style="border-radius:20px">
                <div class="feat-icon w-14 h-14 rounded-2xl flex items-center justify-center mb-6" style="background:<?= $bg ?>">
                    <i data-lucide="<?= $icon ?>" class="w-7 h-7" style="color:<?= $color ?>"></i>
                </div>
                <h3 class="font-display text-neutral-900 mb-3" style="font-size:1.2rem"><?= $title ?></h3>
                <p class="text-neutral-500 text-sm leading-relaxed" style="font-family:'DM Sans',sans-serif"><?= $desc ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── HOW IT WORKS ── -->
<section class="py-24">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-16 reveal">
            <p class="section-tag mb-3">How It Works</p>
            <h2 class="font-display text-neutral-900" style="font-size:clamp(1.8rem,4vw,2.8rem)">Three steps.<br><em>That's all it takes.</em></h2>
        </div>
        <div class="grid sm:grid-cols-3 gap-10 relative">
            <!-- Connector line (desktop) -->
            <div class="hidden sm:block absolute top-7 left-1/6 right-1/6 h-px" style="background:linear-gradient(90deg,transparent,var(--border),var(--border),transparent)"></div>
            <?php foreach([['Choose type','Pick from 24+ QR types — URL, WiFi, contact, payment, and more.','sparkles'],['Enter data','Fill in the details and customise colors to match your brand.','pen-line'],['Download','Export as PNG, SVG, or PDF. Use it anywhere.','download']] as $i=>[$title,$desc,$icon]): ?>
            <div class="text-center reveal delay-<?= $i+1 ?>">
                <div class="relative inline-block mb-5">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white mx-auto font-display font-bold text-lg" style="background:var(--orange);box-shadow:0 4px 16px rgba(255,92,53,.3)"><?= $i+1 ?></div>
                </div>
                <h3 class="font-display text-neutral-900 mb-2" style="font-size:1.1rem"><?= $title ?></h3>
                <p class="text-sm text-neutral-500 leading-relaxed" style="font-family:'DM Sans',sans-serif"><?= $desc ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── INTERACTIVE "TRY IT" SECTION ── -->
<section class="py-16 px-4" style="background:var(--bg2)">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-10 reveal">
            <p class="section-tag mb-3">Live Demo</p>
            <h2 class="font-display text-neutral-900 mb-3" style="font-size:clamp(1.8rem,4vw,2.6rem)">Try it right now.<br><em>No sign-up needed.</em></h2>
            <p class="text-neutral-500" style="font-family:'DM Sans',sans-serif">Type anything below and watch your QR appear instantly.</p>
        </div>
        <div class="card p-6 sm:p-8 reveal shadow-xl" style="border-radius:24px">
            <div class="grid sm:grid-cols-2 gap-8 items-center">
                <div class="space-y-4">
                    <!-- Type selector pills -->
                    <div class="flex flex-wrap gap-2">
                        <?php foreach(['url'=>'URL','text'=>'Text','phone'=>'Phone','email'=>'Email'] as $k=>$lab): ?>
                        <button onclick="setTryType('<?= $k ?>')" data-try="<?= $k ?>"
                            class="try-type-btn px-3 py-1.5 rounded-lg text-xs font-bold border transition-all" style="font-family:'DM Sans',sans-serif;border-color:var(--border);color:var(--text2)">
                            <?= $lab ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <label class="label">Your content</label>
                        <input id="try-input" type="text" class="field" placeholder="https://yourwebsite.com" value="https://qrpro.app">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">Foreground</label>
                            <input type="color" id="try-fg" value="#111110" style="height:40px">
                        </div>
                        <div>
                            <label class="label">Background</label>
                            <input type="color" id="try-bg" value="#ffffff" style="height:40px">
                        </div>
                    </div>
                    <a href="<?= url('/register') ?>" class="btn btn-p w-full justify-center" style="font-size:14px">
                        <i data-lucide="download" class="w-4 h-4"></i> Download this QR Free
                    </a>
                </div>
                <!-- QR output -->
                <div class="flex flex-col items-center gap-4">
                    <div class="p-5 rounded-2xl shadow-inner" style="background:white;border:1px solid var(--border)">
                        <div id="try-qr" style="width:180px;height:180px"></div>
                    </div>
                    <p class="text-xs text-neutral-400 font-medium" style="font-family:'DM Sans',sans-serif">Updates as you type</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ── -->
<section class="py-14 px-4">
    <div class="max-w-4xl mx-auto reveal">
        <div class="relative overflow-hidden rounded-3xl p-10 sm:p-16 text-center" style="background:linear-gradient(135deg,var(--orange) 0%,#FF8C5E 50%,#FFAE80 100%)">
            <div class="absolute inset-0 bg-dots opacity-20"></div>
            <div class="absolute top-0 right-0 w-64 h-64 rounded-full" style="background:rgba(255,255,255,.07);transform:translate(30%,-30%)"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 rounded-full" style="background:rgba(255,255,255,.07);transform:translate(-30%,30%)"></div>
            <div class="relative z-10">
                <h2 class="font-display font-bold text-white mb-4" style="font-size:clamp(1.8rem,4vw,2.8rem)">Ready to create?</h2>
                <p class="text-white/80 mb-8 max-w-lg mx-auto" style="font-family:'DM Sans',sans-serif;font-size:17px">Join thousands generating professional QR codes daily. Free forever, no credit card needed.</p>
                <a href="<?= $currentUser ? url('/generate') : url('/register') ?>" class="magnetic inline-flex items-center gap-2 px-8 py-4 rounded-2xl font-bold bg-white hover:bg-orange-50 transition-all shadow-xl" style="color:var(--orange);font-family:'DM Sans',sans-serif;font-size:15px">
                    <i data-lucide="zap" class="w-5 h-5"></i> Start for free — takes 5 seconds
                </a>
            </div>
        </div>
    </div>
</section>

<script>
// ── Cursor glow ──
const glow = document.getElementById('cursor-glow');
if(glow){
    document.addEventListener('mousemove', e => {
        glow.style.left = e.clientX + 'px';
        glow.style.top = e.clientY + 'px';
    });
}

// ── Blob parallax ──
const blob1 = document.getElementById('blob1');
document.addEventListener('mousemove', e => {
    if(!blob1) return;
    const x = (e.clientX / window.innerWidth - .5) * 40;
    const y = (e.clientY / window.innerHeight - .5) * 40;
    blob1.style.transform = `translate(${x}px, ${y}px)`;
});

// ── Card 3D tilt ──
const heroCard = document.getElementById('hero-card');
if(heroCard){
    heroCard.addEventListener('mousemove', e => {
        const rect = heroCard.getBoundingClientRect();
        const x = (e.clientX - rect.left) / rect.width - .5;
        const y = (e.clientY - rect.top) / rect.height - .5;
        heroCard.style.transform = `perspective(800px) rotateY(${x*8}deg) rotateX(${-y*8}deg)`;
    });
    heroCard.addEventListener('mouseleave', () => {
        heroCard.style.transform = 'perspective(800px) rotateY(0) rotateX(0)';
    });
}

// ── Live QR in hero card ──
let heroQR = null;
let heroDebounce = null;
function makeHeroQR(content, fg, bg) {
    const wrap = document.getElementById('live-qr');
    wrap.innerHTML = '';
    try {
        heroQR = new QRCode(wrap, {
            text: content || ' ',
            width: 140, height: 140,
            colorDark: fg || '#111110',
            colorLight: bg || '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    } catch(e){}
}
makeHeroQR('https://qrpro.app', '#111110', '#ffffff');

const demoInput = document.getElementById('demo-input');
const demoFg = document.getElementById('demo-fg');
const demoBg = document.getElementById('demo-bg');
function refreshHeroQR() {
    clearTimeout(heroDebounce);
    heroDebounce = setTimeout(() => {
        makeHeroQR(demoInput.value || 'https://qrpro.app', demoFg.value, demoBg.value);
    }, 350);
}
demoInput?.addEventListener('input', refreshHeroQR);
demoFg?.addEventListener('input', refreshHeroQR);
demoBg?.addEventListener('input', refreshHeroQR);

// ── Hero demo tab switching ──
const demoContents = {
    url:      { placeholder:'https://yourwebsite.com', value:'https://qrpro.app', label:'Enter URL to preview →' },
    wifi:     { placeholder:'WiFi network name', value:'MyHomeWifi', label:'Enter WiFi SSID →' },
    whatsapp: { placeholder:'+1234567890', value:'+919876543210', label:'Enter phone number →' },
    text:     { placeholder:'Any text message...', value:'Hello from QRPro!', label:'Enter text →' },
};
function switchDemoTab(tab) {
    document.querySelectorAll('.qr-tab').forEach(b => {
        b.classList.remove('active-tab');
    });
    document.querySelector(`.qr-tab[data-tab="${tab}"]`)?.classList.add('active-tab');
    const cfg = demoContents[tab] || demoContents.url;
    if(demoInput) {
        demoInput.placeholder = cfg.placeholder;
        demoInput.value = cfg.value;
        document.querySelector('#demo-input-wrap .label').textContent = cfg.label;
    }
    let content = cfg.value;
    if(tab==='wifi') content = `WIFI:T:WPA;S:${cfg.value};P:;;`;
    if(tab==='whatsapp') content = `https://wa.me/${cfg.value.replace(/\D/g,'')}`;
    makeHeroQR(content, demoFg?.value, demoBg?.value);
}
// Activate first tab
document.querySelector('.qr-tab[data-tab="url"]')?.classList.add('active-tab');

// ── "Try it" section ──
let tryQR = null;
let tryDebounce = null;
let tryType = 'url';
function buildTryContent(val) {
    if(tryType==='phone') return `tel:${val}`;
    if(tryType==='email') return `mailto:${val}`;
    if(tryType==='url' && val && !val.startsWith('http')) return 'https://'+val;
    return val || ' ';
}
function makeTryQR() {
    const wrap = document.getElementById('try-qr');
    if(!wrap) return;
    const val = document.getElementById('try-input')?.value;
    const fg = document.getElementById('try-fg')?.value || '#111110';
    const bg = document.getElementById('try-bg')?.value || '#ffffff';
    wrap.innerHTML='';
    try {
        new QRCode(wrap, { text:buildTryContent(val), width:180, height:180, colorDark:fg, colorLight:bg, correctLevel:QRCode.CorrectLevel.H });
    } catch(e){}
}
makeTryQR();
['try-input','try-fg','try-bg'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', () => {
        clearTimeout(tryDebounce);
        tryDebounce = setTimeout(makeTryQR, 300);
    });
});

function setTryType(t) {
    tryType = t;
    const placeholders = { url:'https://yourwebsite.com', text:'Any message here...', phone:'+1 234 567 8900', email:'you@email.com' };
    const values = { url:'https://qrpro.app', text:'Hello from QRPro!', phone:'+919876543210', email:'hello@qrpro.app' };
    const inp = document.getElementById('try-input');
    if(inp){ inp.placeholder=placeholders[t]; inp.value=values[t]; }
    document.querySelectorAll('.try-type-btn').forEach(b => {
        const active = b.dataset.try===t;
        b.style.background = active ? 'var(--orange)' : '';
        b.style.color = active ? 'white' : '';
        b.style.borderColor = active ? 'var(--orange)' : '';
    });
    makeTryQR();
}
setTryType('url');

// ── Magnetic buttons ──
document.querySelectorAll('.magnetic').forEach(btn => {
    btn.addEventListener('mousemove', e => {
        const rect = btn.getBoundingClientRect();
        const x = (e.clientX - rect.left - rect.width/2) * .25;
        const y = (e.clientY - rect.top - rect.height/2) * .25;
        btn.style.transform = `translate(${x}px,${y}px)`;
    });
    btn.addEventListener('mouseleave', () => { btn.style.transform = ''; });
});

// ── Type grid stagger in on scroll ──
const gridItems = document.querySelectorAll('.type-grid-item');
const gridObs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if(entry.isIntersecting) {
            const items = entry.target.querySelectorAll ? [entry.target] : [];
            entry.target.classList.add('in');
        }
    });
}, { threshold: .1 });
gridItems.forEach((el,i) => {
    el.style.transitionDelay = (i%6 * 0.06) + 's';
    gridObs.observe(el);
});

// ── Scroll reveal ──
const revObs = new IntersectionObserver(entries => {
    entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold:.12 });
document.querySelectorAll('.reveal').forEach(el => revObs.observe(el));

// ── Type card color on hover ──
document.querySelectorAll('.type-card').forEach(card => {
    const color = card.style.getPropertyValue('--hover-color');
    card.addEventListener('mouseenter', () => {
        card.style.borderColor = color + '50';
        card.style.boxShadow = `0 8px 32px ${color}20`;
        card.querySelector('.w-11')?.style && (card.querySelector('.w-11').style.background = color+'25');
    });
    card.addEventListener('mouseleave', () => {
        card.style.borderColor = '';
        card.style.boxShadow = '';
        if(card.querySelector('.w-11')) card.querySelector('.w-11').style.background = '';
    });
});

document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>