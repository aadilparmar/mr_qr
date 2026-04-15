<?php
// ── Auth + logic BEFORE any HTML output ───────────────────────────────────────
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$currentUser  = getCurrentUser();
$types        = getQRTypes();
$selectedType = $_GET['type'] ?? 'url';
if (!isset($types[$selectedType]) || $selectedType === 'bulk') $selectedType = 'url';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_qr'])) {
    $type    = $_POST['qr_type'] ?? 'url';
    if (!array_key_exists($type, $types)) $type = 'url';
    $title    = trim($_POST['qr_title']  ?? '');
    $content  = $_POST['qr_content']     ?? '';
    $filename = generateFilename($type);
    $settings = [
        'fg_color'         => $_POST['fg_color']         ?? '#000000',
        'bg_color'         => $_POST['bg_color']         ?? '#ffffff',
        'size'             => $_POST['qr_size']          ?? 256,
        'error_correction' => $_POST['error_correction'] ?? 'H',
    ];
    if ($currentUser) {
        saveQRCode($currentUser['id'], $type, $title ?: ($types[$type]['name'] ?? $type), $content, $filename, $settings);
        setFlash('success', 'QR Code saved to your history!');
    }
    header('Location: ' . url('/history'));
    exit;
}

// ── Now safe to output HTML ────────────────────────────────────────────────────
$pageTitle = 'Generate QR Code';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.type-pill{display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:99px;font-size:12px;font-weight:600;color:var(--text2);border:1.5px solid var(--border);background:var(--bg);cursor:pointer;transition:all .17s;white-space:nowrap;text-decoration:none;font-family:'DM Sans',sans-serif}
.type-pill:hover{border-color:var(--orange);color:var(--orange);background:var(--orange-light)}
.type-pill.active{background:var(--orange);border-color:var(--orange);color:#fff;box-shadow:0 3px 12px rgba(255,92,53,.28)}
.type-scroll{overflow-x:auto;scrollbar-width:none;padding-bottom:2px}
.type-scroll::-webkit-scrollbar{display:none}
#qr-code{border-radius:12px;overflow:hidden}
.qr-frame{background:#fff;border:1.5px solid var(--border);border-radius:18px;display:flex;align-items:center;justify-content:center;min-height:240px;transition:box-shadow .3s,border-color .3s;padding:20px}
.qr-frame.has-qr{box-shadow:0 6px 32px rgba(0,0,0,.1);border-color:var(--border2)}
.action-btn{display:flex;align-items:center;justify-content:center;gap:6px;padding:10px 14px;border-radius:10px;font-size:12.5px;font-weight:700;cursor:pointer;transition:all .17s;border:1.5px solid var(--border);background:var(--bg);color:var(--text2);font-family:'DM Sans',sans-serif;flex:1}
.action-btn:hover:not(:disabled){background:var(--bg2);border-color:var(--border2);color:var(--text);transform:translateY(-1px)}
.action-btn.primary{background:var(--orange);border-color:var(--orange);color:#fff;box-shadow:0 3px 10px rgba(255,92,53,.28)}
.action-btn.primary:hover:not(:disabled){background:#f04020;transform:translateY(-1px)}
.action-btn:disabled{opacity:.35;cursor:not-allowed;transform:none!important}
.cust-card{background:var(--bg);border:1px solid var(--border);border-radius:18px}
.cust-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;cursor:pointer;user-select:none}
.cust-body{overflow:hidden;transition:max-height .35s cubic-bezier(.22,1,.36,1)}
.cust-body-inner{padding:0 20px 20px;border-top:1px solid var(--border)}
.input-field{width:100%;padding:11px 16px;background:var(--bg);color:var(--text);border:1.5px solid var(--border);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:14px;transition:all .15s}
.input-field:focus{outline:none;border-color:var(--orange);box-shadow:0 0 0 3px rgba(255,92,53,.12)}
.input-field::placeholder{color:var(--text3)}
select.input-field{-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23A09E99' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:40px}
textarea.input-field{resize:vertical}
.chev{transition:transform .3s}
.chev.open{transform:rotate(180deg)}
.color-presets{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:4px}
.color-preset{width:32px;height:32px;border-radius:50%;cursor:pointer;border:2.5px solid var(--border);transition:all .17s;position:relative;overflow:hidden}
.color-preset:hover{transform:scale(1.15);box-shadow:0 3px 12px rgba(0,0,0,.15)}
.color-preset.active-preset{border-color:var(--orange);box-shadow:0 0 0 2px var(--orange)}
.color-preset::before{content:'';position:absolute;top:0;left:0;width:50%;height:100%}
.color-preset::after{content:'';position:absolute;top:0;right:0;width:50%;height:100%}
.color-preset[data-tooltip]{position:relative}
.color-preset[data-tooltip]:hover::after{content:attr(data-tooltip);position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#1E293B;color:#fff;font-size:10px;font-weight:600;padding:3px 8px;border-radius:6px;white-space:nowrap;z-index:10;width:auto;height:auto;right:auto;top:auto}
.cust-section{margin-bottom:3px}
.toggle-row{display:flex;align-items:center;justify-content:space-between;padding:4px 0}
.toggle-label{font-size:12px;font-weight:600;color:var(--text2)}
.toggle-switch{position:relative;width:36px;height:20px;background:var(--border);border-radius:10px;cursor:pointer;transition:background .2s}
.toggle-switch.active{background:var(--orange)}
.toggle-switch::after{content:'';position:absolute;top:2px;left:2px;width:16px;height:16px;background:#fff;border-radius:50%;transition:transform .2s}
.toggle-switch.active::after{transform:translateX(16px)}
.gradient-opts{overflow:hidden;max-height:0;transition:max-height .3s ease}
.gradient-opts.show{max-height:300px}
.slider-row{display:flex;align-items:center;gap:8px}
.slider-row input[type=range]{flex:1;accent-color:var(--orange)}
.slider-val{font-size:11px;font-weight:700;color:var(--orange);min-width:32px;text-align:right}
.cust-card+.cust-card{margin-top:3px}

/* ═══ Custom Select Dropdown ═══ */
.cs-wrap{position:relative}
.cs-trigger{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--bg);border:1.5px solid var(--border);border-radius:10px;cursor:pointer;font-size:13px;font-weight:500;color:var(--text);transition:all .15s;user-select:none}
.cs-trigger:hover{border-color:var(--orange)}
.cs-trigger.open{border-color:var(--orange);box-shadow:0 0 0 3px rgba(255,92,53,.1);border-radius:10px 10px 0 0}
.cs-trigger .cs-arrow{transition:transform .2s;color:var(--text3)}
.cs-trigger.open .cs-arrow{transform:rotate(180deg);color:var(--orange)}
.cs-trigger .cs-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-right:6px}
.cs-dropdown{position:absolute;top:100%;left:0;right:0;background:var(--bg);border:1.5px solid var(--orange);border-top:none;border-radius:0 0 10px 10px;box-shadow:0 12px 32px rgba(0,0,0,.12);z-index:60;max-height:220px;overflow-y:auto;padding:4px;display:none;scrollbar-width:thin}
.cs-dropdown.show{display:block;animation:scaleIn .15s ease}
.cs-dropdown::-webkit-scrollbar{width:4px}
.cs-dropdown::-webkit-scrollbar-thumb{background:var(--border2);border-radius:2px}
.cs-option{display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:all .1s;color:var(--text2)}
.cs-option:hover{background:var(--orange-light);color:var(--orange)}
.cs-option.selected{background:var(--orange);color:#fff;font-weight:600}
.cs-option .cs-check{margin-left:auto;opacity:0}
.cs-option.selected .cs-check{opacity:1}

/* ═══ Custom Color Picker ═══ */
.cp-wrap{position:relative}
.cp-trigger{display:flex;align-items:center;gap:10px;padding:7px 12px;background:var(--bg);border:1.5px solid var(--border);border-radius:10px;cursor:pointer;transition:all .15s;width:100%}
.cp-trigger:hover{border-color:var(--orange)}
.cp-swatch{width:26px;height:26px;border-radius:7px;border:1.5px solid rgba(0,0,0,.08);flex-shrink:0;transition:transform .15s}
.cp-trigger:hover .cp-swatch{transform:scale(1.1)}
.cp-hex{font-size:12px;font-weight:600;font-family:'JetBrains Mono',monospace;color:var(--text);flex:1}
.cp-label{font-size:10px;color:var(--text3);font-weight:500}
.cp-panel{position:absolute;top:calc(100% + 6px);left:0;width:260px;background:var(--bg);border:1.5px solid var(--border);border-radius:14px;box-shadow:0 15px 50px rgba(0,0,0,.18);z-index:70;padding:14px;display:none}
.cp-panel.show{display:block;animation:scaleIn .15s ease}
.cp-panel-title{font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px}
.cp-grid{display:grid;grid-template-columns:repeat(8,1fr);gap:5px;margin-bottom:12px}
.cp-color{width:100%;aspect-ratio:1;border-radius:5px;cursor:pointer;border:2px solid transparent;transition:all .12s}
.cp-color:hover{transform:scale(1.2);z-index:1;border-color:var(--text2)}
.cp-color.active{border-color:var(--orange);box-shadow:0 0 0 2px var(--orange)}
.cp-input-row{display:flex;gap:6px;align-items:center}
.cp-native{width:34px;height:34px;border-radius:8px;border:1.5px solid var(--border);cursor:pointer;padding:1px;flex-shrink:0}
.cp-hex-input{flex:1;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;font-family:'JetBrains Mono',monospace;color:var(--text);background:var(--bg)}
.cp-hex-input:focus{border-color:var(--orange);outline:none;box-shadow:0 0 0 3px rgba(255,92,53,.1)}
html.dark .cp-panel{box-shadow:0 15px 50px rgba(0,0,0,.4)}
html.dark .cs-dropdown{box-shadow:0 12px 32px rgba(0,0,0,.3)}
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-5 py-8 lg:py-10">

    <div class="mb-6 a-up">
        <p class="section-tag mb-1">Creator Studio</p>
        <h1 class="font-display font-extrabold text-3xl sm:text-4xl text-neutral-900">Generate QR Code</h1>
        <p class="text-neutral-500 mt-1 text-sm">Choose a type, fill the form, download instantly</p>
    </div>

    <!-- Type Selector -->
    <div class="type-scroll flex gap-2 mb-7 a-up d1">
        <?php foreach ($types as $key => $type): if ($key === 'bulk') continue; ?>
        <a href="<?= url('/generate?type=' . $key) ?>"
           class="type-pill <?= $key === $selectedType ? 'active' : '' ?>">
            <i data-lucide="<?= $type['icon'] ?>" style="width:18px;height:18px"></i>
            <span><?= $type['name'] ?></span>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="grid lg:grid-cols-5 gap-5">

        <!-- LEFT: Form + Customise -->
        <div class="lg:col-span-3 space-y-4 a-up d2">

            <div class="card p-6" style="border-radius:20px">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-13 h-13 rounded-xl flex items-center justify-center flex-shrink-0" style="width:52px;height:52px;background:<?= $types[$selectedType]['color'] ?>15;border:1.5px solid <?= $types[$selectedType]['color'] ?>25">
                        <i data-lucide="<?= $types[$selectedType]['icon'] ?>" class="w-7 h-7" style="color:<?= $types[$selectedType]['color'] ?>"></i>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-lg text-neutral-900 leading-tight"><?= $types[$selectedType]['name'] ?> QR Code</h2>
                        <p class="text-xs text-neutral-400 mt-0.5 font-medium"><?= $types[$selectedType]['desc'] ?></p>
                    </div>
                </div>

                <div id="qr-form-fields" class="space-y-4">
                    <?php switch ($selectedType):
                        case 'url': ?>
                        <div><label class="label">Website URL <span class="text-red-400">*</span></label>
                        <input type="url" id="qr-url" class="input-field" placeholder="https://example.com" oninput="updateQR()"></div>
                        <?php break; case 'text': ?>
                        <div><label class="label">Text Content <span class="text-red-400">*</span></label>
                        <textarea id="qr-text" rows="5" class="input-field" placeholder="Enter your text here..." oninput="updateQR()" style="resize:vertical"></textarea></div>
                        <?php break; case 'email': ?>
                        <div><label class="label">Email Address <span class="text-red-400">*</span></label><input type="email" id="qr-email" class="input-field" placeholder="hello@example.com" oninput="updateQR()"></div>
                        <div><label class="label">Subject</label><input type="text" id="qr-email-subject" class="input-field" placeholder="Email subject" oninput="updateQR()"></div>
                        <div><label class="label">Body</label><textarea id="qr-email-body" rows="3" class="input-field" placeholder="Email body" oninput="updateQR()"></textarea></div>
                        <?php break; case 'phone': ?>
                        <div><label class="label">Phone Number <span class="text-red-400">*</span></label>
                        <input type="tel" id="qr-phone" class="input-field" placeholder="+91 98765 43210" oninput="updateQR()"></div>
                        <?php break; case 'sms': ?>
                        <div><label class="label">Phone Number <span class="text-red-400">*</span></label><input type="tel" id="qr-sms-phone" class="input-field" placeholder="+91 98765 43210" oninput="updateQR()"></div>
                        <div><label class="label">Message</label><textarea id="qr-sms-message" rows="3" class="input-field" placeholder="Your message..." oninput="updateQR()"></textarea></div>
                        <?php break; case 'whatsapp': ?>
                        <div><label class="label">WhatsApp Number <span class="text-red-400">*</span></label><input type="tel" id="qr-wa-phone" class="input-field" placeholder="+91 98765 43210" oninput="updateQR()"></div>
                        <div><label class="label">Pre-filled Message</label><textarea id="qr-wa-message" rows="3" class="input-field" placeholder="Hi there!" oninput="updateQR()"></textarea></div>
                        <?php break; case 'wifi': ?>
                        <div><label class="label">Network Name (SSID) <span class="text-red-400">*</span></label><input type="text" id="qr-wifi-ssid" class="input-field" placeholder="MyWiFiNetwork" oninput="updateQR()"></div>
                        <div><label class="label">Password</label><input type="text" id="qr-wifi-password" class="input-field" placeholder="WiFi password" oninput="updateQR()"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="label">Encryption</label>
                            <select id="qr-wifi-encryption" class="input-field" onchange="updateQR()">
                                <option value="WPA">WPA/WPA2</option><option value="WEP">WEP</option><option value="nopass">None</option>
                            </select></div>
                            <div class="flex items-end pb-1"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" id="qr-wifi-hidden" onchange="updateQR()"><span class="label mb-0">Hidden network</span></label></div>
                        </div>
                        <?php break; case 'vcard': ?>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="label">First Name <span class="text-red-400">*</span></label><input type="text" id="qr-vc-fname" class="input-field" placeholder="John" oninput="updateQR()"></div>
                            <div><label class="label">Last Name</label><input type="text" id="qr-vc-lname" class="input-field" placeholder="Doe" oninput="updateQR()"></div>
                        </div>
                        <div><label class="label">Organisation</label><input type="text" id="qr-vc-org" class="input-field" placeholder="Acme Corp" oninput="updateQR()"></div>
                        <div><label class="label">Job Title</label><input type="text" id="qr-vc-title" class="input-field" placeholder="CEO" oninput="updateQR()"></div>
                        <div><label class="label">Phone</label><input type="tel" id="qr-vc-phone" class="input-field" placeholder="+91 98765 43210" oninput="updateQR()"></div>
                        <div><label class="label">Email</label><input type="email" id="qr-vc-email" class="input-field" placeholder="john@example.com" oninput="updateQR()"></div>
                        <div><label class="label">Website</label><input type="url" id="qr-vc-url" class="input-field" placeholder="https://example.com" oninput="updateQR()"></div>
                        <div><label class="label">Address</label><input type="text" id="qr-vc-address" class="input-field" placeholder="123 Main St, City" oninput="updateQR()"></div>
                        <?php break; case 'event': ?>
                        <div><label class="label">Event Title <span class="text-red-400">*</span></label><input type="text" id="qr-ev-title" class="input-field" placeholder="Team Meeting" oninput="updateQR()"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="label">Start</label><input type="datetime-local" id="qr-ev-start" class="input-field" oninput="updateQR()"></div>
                            <div><label class="label">End</label><input type="datetime-local" id="qr-ev-end" class="input-field" oninput="updateQR()"></div>
                        </div>
                        <div><label class="label">Location</label><input type="text" id="qr-ev-location" class="input-field" placeholder="Office / Zoom link" oninput="updateQR()"></div>
                        <div><label class="label">Description</label><textarea id="qr-ev-desc" rows="2" class="input-field" placeholder="Event details..." oninput="updateQR()"></textarea></div>
                        <?php break; case 'geo': ?>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="label">Latitude <span class="text-red-400">*</span></label><input type="number" step="any" id="qr-geo-lat" class="input-field" placeholder="23.0225" oninput="updateQR()"></div>
                            <div><label class="label">Longitude <span class="text-red-400">*</span></label><input type="number" step="any" id="qr-geo-lng" class="input-field" placeholder="72.5714" oninput="updateQR()"></div>
                        </div>
                        <div><label class="label">Search Query (optional)</label><input type="text" id="qr-geo-query" class="input-field" placeholder="Restaurant name..." oninput="updateQR()"></div>
                        <?php break; case 'upi': ?>
                        <div><label class="label">UPI ID <span class="text-red-400">*</span></label><input type="text" id="qr-upi-pa" class="input-field" placeholder="name@upi" oninput="updateQR()"></div>
                        <div><label class="label">Payee Name</label><input type="text" id="qr-upi-pn" class="input-field" placeholder="John Doe" oninput="updateQR()"></div>
                        <div><label class="label">Amount (₹)</label><input type="number" step="0.01" id="qr-upi-am" class="input-field" placeholder="100.00" oninput="updateQR()"></div>
                        <div><label class="label">Note</label><input type="text" id="qr-upi-tn" class="input-field" placeholder="Payment for..." oninput="updateQR()"></div>
                        <?php break; case 'bitcoin': ?>
                        <div><label class="label">Bitcoin Address <span class="text-red-400">*</span></label><input type="text" id="qr-btc-addr" class="input-field" placeholder="1A1zP1..." oninput="updateQR()"></div>
                        <div><label class="label">Amount (BTC)</label><input type="number" step="0.00000001" id="qr-btc-amount" class="input-field" placeholder="0.001" oninput="updateQR()"></div>
                        <?php break; case 'youtube': ?>
                        <div><label class="label">YouTube URL or Video ID <span class="text-red-400">*</span></label><input type="text" id="qr-yt-url" class="input-field" placeholder="https://youtube.com/watch?v=..." oninput="updateQR()"></div>
                        <?php break; case 'twitter': ?>
                        <div><label class="label">Twitter / X Handle <span class="text-red-400">*</span></label>
                        <div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 font-bold">@</span><input type="text" id="qr-tw-handle" class="input-field" style="padding-left:26px" placeholder="username" oninput="updateQR()"></div></div>
                        <?php break; case 'instagram': ?>
                        <div><label class="label">Instagram Handle <span class="text-red-400">*</span></label>
                        <div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 font-bold">@</span><input type="text" id="qr-ig-handle" class="input-field" style="padding-left:26px" placeholder="username" oninput="updateQR()"></div></div>
                        <?php break; case 'facebook': ?>
                        <div><label class="label">Facebook URL or Username <span class="text-red-400">*</span></label><input type="text" id="qr-fb-url" class="input-field" placeholder="https://facebook.com/page or username" oninput="updateQR()"></div>
                        <?php break; case 'linkedin': ?>
                        <div><label class="label">LinkedIn Profile URL or Username <span class="text-red-400">*</span></label><input type="text" id="qr-li-url" class="input-field" placeholder="https://linkedin.com/in/username or username" oninput="updateQR()"></div>
                        <?php break; case 'spotify': ?>
                        <div><label class="label">Spotify URL <span class="text-red-400">*</span></label><input type="url" id="qr-sp-url" class="input-field" placeholder="https://open.spotify.com/track/..." oninput="updateQR()"></div>
                        <?php break; case 'zoom': ?>
                        <div><label class="label">Zoom Meeting URL <span class="text-red-400">*</span></label><input type="url" id="qr-zm-url" class="input-field" placeholder="https://zoom.us/j/..." oninput="updateQR()"></div>
                        <?php break; case 'pdf': ?>
                        <div><label class="label">PDF File URL <span class="text-red-400">*</span></label><input type="url" id="qr-pdf-url" class="input-field" placeholder="https://example.com/file.pdf" oninput="updateQR()"></div>
                        <?php break; case 'image': ?>
                        <div><label class="label">Image URL <span class="text-red-400">*</span></label><input type="url" id="qr-img-url" class="input-field" placeholder="https://example.com/photo.jpg" oninput="updateQR()"></div>
                        <?php break; case 'mecard': ?>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="label">First Name <span class="text-red-400">*</span></label><input type="text" id="qr-mc-fname" class="input-field" placeholder="John" oninput="updateQR()"></div>
                            <div><label class="label">Last Name</label><input type="text" id="qr-mc-lname" class="input-field" placeholder="Doe" oninput="updateQR()"></div>
                        </div>
                        <div><label class="label">Phone</label><input type="tel" id="qr-mc-phone" class="input-field" placeholder="+91 98765 43210" oninput="updateQR()"></div>
                        <div><label class="label">Email</label><input type="email" id="qr-mc-email" class="input-field" placeholder="john@example.com" oninput="updateQR()"></div>
                        <?php break; case 'appstore': ?>
                        <div><label class="label">App Store / Play Store URL <span class="text-red-400">*</span></label><input type="url" id="qr-app-url" class="input-field" placeholder="https://apps.apple.com/... or https://play.google.com/..." oninput="updateQR()"></div>
                        <?php break; default: ?>
                        <div><label class="label">Content <span class="text-red-400">*</span></label><textarea id="qr-text" rows="4" class="input-field" placeholder="Enter content..." oninput="updateQR()"></textarea></div>
                    <?php endswitch; ?>
                </div>
            </div>

            <!-- ═══ Section 1: Color Presets ═══ -->
            <div class="cust-card a-up d3">
                <div class="cust-head" onclick="toggleCust(this)">
                    <span class="font-display font-bold text-sm flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:linear-gradient(135deg,var(--orange),#F97316)">
                            <i data-lucide="palette" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        Color Presets
                        <span class="badge badge-o" style="font-size:9px">8</span>
                    </span>
                    <i data-lucide="chevron-down" class="chev open w-4 h-4 text-neutral-400"></i>
                </div>
                <div class="cust-body" style="max-height:3000px;overflow:visible">
                    <div class="cust-body-inner pt-4 space-y-3">
                        <div class="color-presets">
                            <div class="color-preset active-preset" data-tooltip="Classic" data-fg="#000000" data-bg="#FFFFFF" onclick="applyPreset(this)" style="background:linear-gradient(90deg,#000000 50%,#FFFFFF 50%);border-color:var(--orange)"></div>
                            <div class="color-preset" data-tooltip="Ocean" data-fg="#1B3A5C" data-bg="#E8F4FD" onclick="applyPreset(this)" style="background:linear-gradient(90deg,#1B3A5C 50%,#E8F4FD 50%)"></div>
                            <div class="color-preset" data-tooltip="Forest" data-fg="#1A5D1A" data-bg="#E8F5E9" onclick="applyPreset(this)" style="background:linear-gradient(90deg,#1A5D1A 50%,#E8F5E9 50%)"></div>
                            <div class="color-preset" data-tooltip="Royal" data-fg="#4F46E5" data-bg="#EEF2FF" onclick="applyPreset(this)" style="background:linear-gradient(90deg,#4F46E5 50%,#EEF2FF 50%)"></div>
                            <div class="color-preset" data-tooltip="Sunset" data-fg="#B91C1C" data-bg="#FFF7ED" onclick="applyPreset(this)" style="background:linear-gradient(90deg,#B91C1C 50%,#FFF7ED 50%)"></div>
                            <div class="color-preset" data-tooltip="Midnight" data-fg="#FFFFFF" data-bg="#1E293B" onclick="applyPreset(this)" style="background:linear-gradient(90deg,#FFFFFF 50%,#1E293B 50%)"></div>
                            <div class="color-preset" data-tooltip="Coral" data-fg="#FF6B6B" data-bg="#FFF5F5" onclick="applyPreset(this)" style="background:linear-gradient(90deg,#FF6B6B 50%,#FFF5F5 50%)"></div>
                            <div class="color-preset" data-tooltip="Gold" data-fg="#B8860B" data-bg="#FFFDE7" onclick="applyPreset(this)" style="background:linear-gradient(90deg,#B8860B 50%,#FFFDE7 50%)"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ Section 2: Dot Style & Color ═══ -->
            <div class="cust-card">
                <div class="cust-head" onclick="toggleCust(this)">
                    <span class="font-display font-bold text-sm flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-orange-500 flex items-center justify-center">
                            <i data-lucide="circle-dot" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        Dot Style & Color
                        <span class="badge badge-o" style="font-size:9px">9</span>
                    </span>
                    <i data-lucide="chevron-down" class="chev w-4 h-4 text-neutral-400"></i>
                </div>
                <div class="cust-body" style="max-height:0">
                    <div class="cust-body-inner pt-4 space-y-3">
                        <div>
                            <label class="label">Dot Type</label>
                            <select id="qr-dot-type" class="input-field" onchange="updateQR()">
                                <option value="square">Square</option>
                                <option value="dots">Dots</option>
                                <option value="rounded" selected>Rounded</option>
                                <option value="extra-rounded">Extra Rounded</option>
                                <option value="classy">Classy</option>
                                <option value="classy-rounded">Classy Rounded</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Dot Color</label>
                            <div class="flex gap-2 items-center">
                                <input type="color" id="qr-fg-color" value="#000000" onchange="document.getElementById('qr-fg-hex').value=this.value;updateQR()" class="w-10 h-10 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                                <input type="text" id="qr-fg-hex" value="#000000" maxlength="7" class="input-field" style="padding:8px 12px" onchange="document.getElementById('qr-fg-color').value=this.value;updateQR()">
                            </div>
                        </div>
                        <div class="toggle-row">
                            <span class="toggle-label">Gradient</span>
                            <div class="toggle-switch" id="dot-grad-toggle" onclick="toggleSwitch(this);toggleGradient('dot-grad-opts',this.classList.contains('active'));updateQR()"></div>
                        </div>
                        <div class="gradient-opts" id="dot-grad-opts">
                            <div class="space-y-3 pt-2">
                                <div>
                                    <label class="label">Gradient Type</label>
                                    <select id="dot-grad-type" class="input-field" onchange="updateQR()">
                                        <option value="linear">Linear</option>
                                        <option value="radial">Radial</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">Start Color</label>
                                        <input type="color" id="dot-grad-start" value="#000000" onchange="updateQR()" class="w-full h-9 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                                    </div>
                                    <div>
                                        <label class="label">End Color</label>
                                        <input type="color" id="dot-grad-end" value="#4F46E5" onchange="updateQR()" class="w-full h-9 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                                    </div>
                                </div>
                                <div>
                                    <label class="label">Angle: <span id="dot-grad-angle-val">0</span>&deg;</label>
                                    <div class="slider-row">
                                        <input type="range" id="dot-grad-angle" min="0" max="360" value="0" oninput="document.getElementById('dot-grad-angle-val').textContent=this.value;updateQR()">
                                        <span class="slider-val" id="dot-grad-angle-val2">0&deg;</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ Section 3: Background ═══ -->
            <div class="cust-card">
                <div class="cust-head" onclick="toggleCust(this)">
                    <span class="font-display font-bold text-sm flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-sky-500 flex items-center justify-center">
                            <i data-lucide="image" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        Background
                        <span class="badge badge-o" style="font-size:9px">8</span>
                    </span>
                    <i data-lucide="chevron-down" class="chev w-4 h-4 text-neutral-400"></i>
                </div>
                <div class="cust-body" style="max-height:0">
                    <div class="cust-body-inner pt-4 space-y-3">
                        <div>
                            <label class="label">Background Color</label>
                            <div class="flex gap-2 items-center">
                                <input type="color" id="qr-bg-color" value="#ffffff" onchange="document.getElementById('qr-bg-hex').value=this.value;updateQR()" class="w-10 h-10 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                                <input type="text" id="qr-bg-hex" value="#ffffff" maxlength="7" class="input-field" style="padding:8px 12px" onchange="document.getElementById('qr-bg-color').value=this.value;updateQR()">
                            </div>
                        </div>
                        <div class="toggle-row">
                            <span class="toggle-label">Transparent Background</span>
                            <div class="toggle-switch" id="bg-transparent-toggle" onclick="toggleSwitch(this);updateQR()"></div>
                        </div>
                        <div class="toggle-row">
                            <span class="toggle-label">Gradient</span>
                            <div class="toggle-switch" id="bg-grad-toggle" onclick="toggleSwitch(this);toggleGradient('bg-grad-opts',this.classList.contains('active'));updateQR()"></div>
                        </div>
                        <div class="gradient-opts" id="bg-grad-opts">
                            <div class="space-y-3 pt-2">
                                <div>
                                    <label class="label">Gradient Type</label>
                                    <select id="bg-grad-type" class="input-field" onchange="updateQR()">
                                        <option value="linear">Linear</option>
                                        <option value="radial">Radial</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">Start Color</label>
                                        <input type="color" id="bg-grad-start" value="#ffffff" onchange="updateQR()" class="w-full h-9 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                                    </div>
                                    <div>
                                        <label class="label">End Color</label>
                                        <input type="color" id="bg-grad-end" value="#EEF2FF" onchange="updateQR()" class="w-full h-9 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                                    </div>
                                </div>
                                <div>
                                    <label class="label">Angle: <span id="bg-grad-angle-val">0</span>&deg;</label>
                                    <div class="slider-row">
                                        <input type="range" id="bg-grad-angle" min="0" max="360" value="0" oninput="document.getElementById('bg-grad-angle-val').textContent=this.value;updateQR()">
                                        <span class="slider-val">0&deg;</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="toggle-row">
                            <span class="toggle-label">Rounded Background</span>
                            <div class="toggle-switch" id="bg-rounded-toggle" onclick="toggleSwitch(this);updateQR()"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ Section 4: Eye Frame (Corner Squares) ═══ -->
            <div class="cust-card">
                <div class="cust-head" onclick="toggleCust(this)">
                    <span class="font-display font-bold text-sm flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-purple-500 flex items-center justify-center">
                            <i data-lucide="scan" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        Eye Frame
                        <span class="badge badge-o" style="font-size:9px">7</span>
                    </span>
                    <i data-lucide="chevron-down" class="chev w-4 h-4 text-neutral-400"></i>
                </div>
                <div class="cust-body" style="max-height:0">
                    <div class="cust-body-inner pt-4 space-y-3">
                        <div>
                            <label class="label">Frame Style</label>
                            <select id="qr-corner-square" class="input-field" onchange="updateQR()">
                                <option value="">Default</option>
                                <option value="dot">Dot</option>
                                <option value="square">Square</option>
                                <option value="extra-rounded" selected>Rounded</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Frame Color</label>
                            <div class="flex gap-2 items-center">
                                <input type="color" id="qr-corner-sq-color" value="#000000" onchange="updateQR()" class="w-10 h-10 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                                <label class="flex items-center gap-1.5 cursor-pointer"><input type="checkbox" id="qr-corner-sq-custom" onchange="updateQR()" style="accent-color:var(--orange)"><span class="text-xs text-neutral-500 font-medium">Custom color</span></label>
                            </div>
                        </div>
                        <div class="toggle-row">
                            <span class="toggle-label">Gradient</span>
                            <div class="toggle-switch" id="csq-grad-toggle" onclick="toggleSwitch(this);toggleGradient('csq-grad-opts',this.classList.contains('active'));updateQR()"></div>
                        </div>
                        <div class="gradient-opts" id="csq-grad-opts">
                            <div class="space-y-3 pt-2">
                                <div>
                                    <label class="label">Gradient Type</label>
                                    <select id="csq-grad-type" class="input-field" onchange="updateQR()">
                                        <option value="linear">Linear</option>
                                        <option value="radial">Radial</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">Start Color</label>
                                        <input type="color" id="csq-grad-start" value="#4F46E5" onchange="updateQR()" class="w-full h-9 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                                    </div>
                                    <div>
                                        <label class="label">End Color</label>
                                        <input type="color" id="csq-grad-end" value="#7C3AED" onchange="updateQR()" class="w-full h-9 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                                    </div>
                                </div>
                                <div>
                                    <label class="label">Angle: <span id="csq-grad-angle-val">0</span>&deg;</label>
                                    <div class="slider-row">
                                        <input type="range" id="csq-grad-angle" min="0" max="360" value="0" oninput="document.getElementById('csq-grad-angle-val').textContent=this.value;updateQR()">
                                        <span class="slider-val">0&deg;</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ Section 5: Eye Dot (Corner Dots) ═══ -->
            <div class="cust-card">
                <div class="cust-head" onclick="toggleCust(this)">
                    <span class="font-display font-bold text-sm flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-rose-500 flex items-center justify-center">
                            <i data-lucide="focus" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        Eye Dot
                        <span class="badge badge-o" style="font-size:9px">7</span>
                    </span>
                    <i data-lucide="chevron-down" class="chev w-4 h-4 text-neutral-400"></i>
                </div>
                <div class="cust-body" style="max-height:0">
                    <div class="cust-body-inner pt-4 space-y-3">
                        <div>
                            <label class="label">Dot Style</label>
                            <select id="qr-corner-dot" class="input-field" onchange="updateQR()">
                                <option value="">Default</option>
                                <option value="dot" selected>Dot</option>
                                <option value="square">Square</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Dot Color</label>
                            <div class="flex gap-2 items-center">
                                <input type="color" id="qr-corner-dot-color" value="#000000" onchange="updateQR()" class="w-10 h-10 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                                <label class="flex items-center gap-1.5 cursor-pointer"><input type="checkbox" id="qr-corner-dot-custom" onchange="updateQR()" style="accent-color:var(--orange)"><span class="text-xs text-neutral-500 font-medium">Custom color</span></label>
                            </div>
                        </div>
                        <div class="toggle-row">
                            <span class="toggle-label">Gradient</span>
                            <div class="toggle-switch" id="cd-grad-toggle" onclick="toggleSwitch(this);toggleGradient('cd-grad-opts',this.classList.contains('active'));updateQR()"></div>
                        </div>
                        <div class="gradient-opts" id="cd-grad-opts">
                            <div class="space-y-3 pt-2">
                                <div>
                                    <label class="label">Gradient Type</label>
                                    <select id="cd-grad-type" class="input-field" onchange="updateQR()">
                                        <option value="linear">Linear</option>
                                        <option value="radial">Radial</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">Start Color</label>
                                        <input type="color" id="cd-grad-start" value="#F43F5E" onchange="updateQR()" class="w-full h-9 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                                    </div>
                                    <div>
                                        <label class="label">End Color</label>
                                        <input type="color" id="cd-grad-end" value="#EC4899" onchange="updateQR()" class="w-full h-9 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                                    </div>
                                </div>
                                <div>
                                    <label class="label">Angle: <span id="cd-grad-angle-val">0</span>&deg;</label>
                                    <div class="slider-row">
                                        <input type="range" id="cd-grad-angle" min="0" max="360" value="0" oninput="document.getElementById('cd-grad-angle-val').textContent=this.value;updateQR()">
                                        <span class="slider-val">0&deg;</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ Section 6: Logo / Watermark ═══ -->
            <div class="cust-card">
                <div class="cust-head" onclick="toggleCust(this)">
                    <span class="font-display font-bold text-sm flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#10B981">
                            <i data-lucide="image-plus" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        Logo / Watermark
                        <span class="badge badge-o" style="font-size:9px">5</span>
                    </span>
                    <i data-lucide="chevron-down" class="chev w-4 h-4 text-neutral-400"></i>
                </div>
                <div class="cust-body" style="max-height:0">
                    <div class="cust-body-inner pt-4 space-y-3">
                        <div>
                            <label class="label">Upload Logo</label>
                            <div class="flex gap-2 items-center">
                                <input type="file" id="qr-logo-file" accept="image/*" onchange="handleLogo(this)" class="input-field" style="padding:8px 12px;font-size:12px">
                                <button type="button" onclick="clearLogo()" class="btn btn-sm btn-s" style="flex-shrink:0;padding:8px 12px">
                                    <i data-lucide="x" style="width:12px;height:12px"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="label">Logo Size: <span id="logo-size-val">30</span>%</label>
                            <div class="slider-row">
                                <input type="range" id="qr-logo-size" min="10" max="50" value="30" oninput="document.getElementById('logo-size-val').textContent=this.value;updateQR()">
                                <span class="slider-val">30%</span>
                            </div>
                        </div>
                        <div>
                            <label class="label">Logo Margin: <span id="logo-margin-val">5</span>px</label>
                            <div class="slider-row">
                                <input type="range" id="qr-logo-margin" min="0" max="20" value="5" oninput="document.getElementById('logo-margin-val').textContent=this.value;updateQR()">
                                <span class="slider-val">5px</span>
                            </div>
                        </div>
                        <div class="toggle-row">
                            <span class="toggle-label">Hide Dots Behind Logo</span>
                            <div class="toggle-switch active" id="logo-hide-dots-toggle" onclick="toggleSwitch(this);updateQR()"></div>
                        </div>
                        <input type="hidden" id="qr-logo-hide-dots" value="1">
                        <div>
                            <label class="label">Logo Opacity: <span id="logo-opacity-val">1.0</span></label>
                            <div class="slider-row">
                                <input type="range" id="qr-logo-opacity" min="0.1" max="1" step="0.1" value="1" oninput="document.getElementById('logo-opacity-val').textContent=parseFloat(this.value).toFixed(1);updateQR()">
                                <span class="slider-val">1.0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ Section 7: Frame & Label ═══ -->
            <div class="cust-card">
                <div class="cust-head" onclick="toggleCust(this)">
                    <span class="font-display font-bold text-sm flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-amber-500 flex items-center justify-center">
                            <i data-lucide="frame" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        Frame & Label
                        <span class="badge badge-o" style="font-size:9px">8</span>
                    </span>
                    <i data-lucide="chevron-down" class="chev w-4 h-4 text-neutral-400"></i>
                </div>
                <div class="cust-body" style="max-height:0">
                    <div class="cust-body-inner pt-4 space-y-3">
                        <div class="toggle-row">
                            <span class="toggle-label">Enable Frame</span>
                            <div class="toggle-switch" id="frame-enable-toggle" onclick="toggleSwitch(this);applyFrameShadowCSS();updateQR()"></div>
                        </div>
                        <input type="hidden" id="frame-enable" value="0">
                        <div id="frame-opts" style="display:none" class="space-y-3">
                            <div>
                                <label class="label">Frame Color</label>
                                <input type="color" id="frame-color" value="#000000" onchange="applyFrameShadowCSS()" class="w-full h-9 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                            </div>
                            <div>
                                <label class="label">Frame Width: <span id="frame-width-val">4</span>px</label>
                                <div class="slider-row">
                                    <input type="range" id="frame-width" min="2" max="15" value="4" oninput="document.getElementById('frame-width-val').textContent=this.value;applyFrameShadowCSS()">
                                    <span class="slider-val">4px</span>
                                </div>
                            </div>
                            <div>
                                <label class="label">Frame Padding: <span id="frame-padding-val">15</span>px</label>
                                <div class="slider-row">
                                    <input type="range" id="frame-padding" min="5" max="40" value="15" oninput="document.getElementById('frame-padding-val').textContent=this.value;applyFrameShadowCSS()">
                                    <span class="slider-val">15px</span>
                                </div>
                            </div>
                            <div>
                                <label class="label">Border Radius: <span id="frame-radius-val">12</span>px</label>
                                <div class="slider-row">
                                    <input type="range" id="frame-radius" min="0" max="30" value="12" oninput="document.getElementById('frame-radius-val').textContent=this.value;applyFrameShadowCSS()">
                                    <span class="slider-val">12px</span>
                                </div>
                            </div>
                            <div>
                                <label class="label">Label Text</label>
                                <input type="text" id="frame-label" class="input-field" placeholder="Scan Me!" oninput="applyFrameShadowCSS()">
                            </div>
                            <div>
                                <label class="label">Label Color</label>
                                <input type="color" id="frame-label-color" value="#ffffff" onchange="applyFrameShadowCSS()" class="w-full h-9 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                            </div>
                            <div>
                                <label class="label">Label Font Size: <span id="frame-label-size-val">14</span>px</label>
                                <div class="slider-row">
                                    <input type="range" id="frame-label-size" min="10" max="24" value="14" oninput="document.getElementById('frame-label-size-val').textContent=this.value;applyFrameShadowCSS()">
                                    <span class="slider-val">14px</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ Section 8: Shadow & Effects ═══ -->
            <div class="cust-card">
                <div class="cust-head" onclick="toggleCust(this)">
                    <span class="font-display font-bold text-sm flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-violet-500 flex items-center justify-center">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        Shadow & Effects
                        <span class="badge badge-o" style="font-size:9px">5</span>
                    </span>
                    <i data-lucide="chevron-down" class="chev w-4 h-4 text-neutral-400"></i>
                </div>
                <div class="cust-body" style="max-height:0">
                    <div class="cust-body-inner pt-4 space-y-3">
                        <div class="toggle-row">
                            <span class="toggle-label">Enable Shadow</span>
                            <div class="toggle-switch" id="shadow-enable-toggle" onclick="toggleSwitch(this);applyFrameShadowCSS()"></div>
                        </div>
                        <input type="hidden" id="shadow-enable" value="0">
                        <div id="shadow-opts" style="display:none" class="space-y-3">
                            <div>
                                <label class="label">Shadow Color</label>
                                <input type="color" id="shadow-color" value="#000000" onchange="applyFrameShadowCSS()" class="w-full h-9 rounded-lg border border-neutral-200 cursor-pointer p-0.5">
                            </div>
                            <div>
                                <label class="label">Shadow Blur: <span id="shadow-blur-val">10</span>px</label>
                                <div class="slider-row">
                                    <input type="range" id="shadow-blur" min="0" max="30" value="10" oninput="document.getElementById('shadow-blur-val').textContent=this.value;applyFrameShadowCSS()">
                                    <span class="slider-val">10px</span>
                                </div>
                            </div>
                            <div>
                                <label class="label">Offset X: <span id="shadow-ox-val">0</span>px</label>
                                <div class="slider-row">
                                    <input type="range" id="shadow-ox" min="-10" max="10" value="0" oninput="document.getElementById('shadow-ox-val').textContent=this.value;applyFrameShadowCSS()">
                                    <span class="slider-val">0px</span>
                                </div>
                            </div>
                            <div>
                                <label class="label">Offset Y: <span id="shadow-oy-val">4</span>px</label>
                                <div class="slider-row">
                                    <input type="range" id="shadow-oy" min="0" max="15" value="4" oninput="document.getElementById('shadow-oy-val').textContent=this.value;applyFrameShadowCSS()">
                                    <span class="slider-val">4px</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ Section 9: Size & Export ═══ -->
            <div class="cust-card">
                <div class="cust-head" onclick="toggleCust(this)">
                    <span class="font-display font-bold text-sm flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-teal-500 flex items-center justify-center">
                            <i data-lucide="ruler" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        Size & Export
                        <span class="badge badge-o" style="font-size:9px">5</span>
                    </span>
                    <i data-lucide="chevron-down" class="chev w-4 h-4 text-neutral-400"></i>
                </div>
                <div class="cust-body" style="max-height:0">
                    <div class="cust-body-inner pt-4 space-y-3">
                        <div>
                            <label class="label">QR Shape</label>
                            <select id="qr-shape" class="input-field" onchange="updateQR()">
                                <option value="square" selected>Square</option>
                                <option value="circle">Circle</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Export Size</label>
                            <select id="qr-size" class="input-field" onchange="updateQR()">
                                <option value="256">256 &times; 256</option>
                                <option value="512">512 &times; 512</option>
                                <option value="1024" selected>1024 &times; 1024</option>
                                <option value="2048">2048 &times; 2048</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Error Correction</label>
                            <select id="qr-ec" class="input-field" onchange="updateQR()">
                                <option value="L">Low (7%)</option>
                                <option value="M">Medium (15%)</option>
                                <option value="Q">Quartile (25%)</option>
                                <option value="H" selected>High (30%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Quiet Zone: <span id="margin-val">10</span>px</label>
                            <div class="slider-row">
                                <input type="range" id="qr-margin" min="0" max="40" value="10" oninput="document.getElementById('margin-val').textContent=this.value;updateQR()">
                                <span class="slider-val">10px</span>
                            </div>
                        </div>
                        <div>
                            <label class="label">Export Format</label>
                            <select id="qr-export-fmt" class="input-field">
                                <option value="png" selected>PNG</option>
                                <option value="svg">SVG</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT: Preview + Download -->
        <div class="lg:col-span-2 a-up d3">
            <div class="sticky top-24 space-y-4">

                <div class="card p-5 text-center" style="border-radius:20px">
                    <p class="text-xs font-bold uppercase tracking-widest mb-4" style="color:var(--text3)">Live Preview</p>

                    <div class="qr-frame mb-4" id="qr-wrapper">
                        <div id="qr-code" style="display:none;border-radius:10px;overflow:hidden"></div>
                        <div id="qr-placeholder" class="flex flex-col items-center gap-3 py-8">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background:var(--bg2);border:2px dashed var(--border2)">
                                <i data-lucide="qr-code" class="w-8 h-8" style="color:var(--text3)"></i>
                            </div>
                            <p class="text-sm font-semibold text-neutral-400">Fill in the form to preview</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <input type="text" id="qr-save-title" class="input-field text-sm text-center"
                               placeholder="Give your QR a name (optional)...">
                    </div>

                    <div class="grid grid-cols-3 gap-2 mb-2">
                        <button onclick="downloadQR('png')" id="btn-download-png" class="action-btn primary" disabled>
                            <i data-lucide="download" style="width:16px;height:16px"></i> PNG
                        </button>
                        <button onclick="downloadQR('svg')" id="btn-download-svg" class="action-btn" disabled>
                            <i data-lucide="download" style="width:16px;height:16px"></i> SVG
                        </button>
                        <button onclick="saveQRToDB()" id="btn-save" class="action-btn" disabled>
                            <i data-lucide="save" style="width:16px;height:16px"></i> Save
                        </button>
                        <button onclick="printQR()" id="btn-print" class="action-btn" disabled>
                            <i data-lucide="printer" style="width:16px;height:16px"></i> Print
                        </button>
                        <button onclick="copyQR()" id="btn-copy" class="action-btn" disabled>
                            <i data-lucide="clipboard" style="width:16px;height:16px"></i> Copy
                        </button>
                        <button onclick="downloadPDF()" id="btn-pdf" class="action-btn" disabled>
                            <i data-lucide="file-text" style="width:16px;height:16px"></i> PDF
                        </button>
                    </div>
                    <button onclick="downloadQR('png')" id="btn-download-big" class="action-btn primary w-full" style="padding:13px;font-size:14px" disabled>
                        <i data-lucide="download" style="width:18px;height:18px"></i> Download QR Code
                    </button>

                    <form id="save-form" method="POST" action="<?= url('/generate?type=' . $selectedType) ?>" style="display:none">
                        <input type="hidden" name="save_qr"          value="1">
                        <input type="hidden" name="qr_type"          value="<?= $selectedType ?>">
                        <input type="hidden" name="qr_title"         id="form-qr-title">
                        <input type="hidden" name="qr_content"       id="form-qr-content">
                        <input type="hidden" name="fg_color"         id="form-fg-color">
                        <input type="hidden" name="bg_color"         id="form-bg-color">
                        <input type="hidden" name="qr_size"          id="form-qr-size">
                        <input type="hidden" name="error_correction" id="form-qr-ec">
                    </form>
                </div>

                <div class="card p-5 a-up d4" style="border-radius:20px">
                    <p class="text-xs font-bold uppercase tracking-widest mb-4" style="color:var(--text3)">Quick Tips</p>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-md flex-shrink-0 flex items-center justify-center mt-0.5" style="background:var(--orange-light)">
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <p class="text-xs text-neutral-500 font-medium leading-relaxed">Use <strong class="text-neutral-700">High (30%)</strong> error correction for best scan reliability</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-md flex-shrink-0 flex items-center justify-center mt-0.5" style="background:#EEF2FF">
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <p class="text-xs text-neutral-500 font-medium leading-relaxed">Download <strong class="text-neutral-700">1024px PNG</strong> for high-quality print use</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-md flex-shrink-0 flex items-center justify-center mt-0.5" style="background:#F0FDF4">
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <p class="text-xs text-neutral-500 font-medium leading-relaxed">Hit <strong class="text-neutral-700">Save</strong> to keep your QR in history and re-download anytime</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qr-code-styling@1.6.0-rc.1/lib/qr-code-styling.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
/* ═══════════════════════════════════════════════════════════════════
   QR Code Pro — Advanced Customisation Engine (62 controls)
   ═══════════════════════════════════════════════════════════════════ */
let qrInstance = null;
let logoDataUrl = '';
let debounceTimer = null;
const currentType = '<?= $selectedType ?>';

/* ── getQRContent (all 23 types — UNCHANGED) ──────────────────────── */
function getQRContent() {
    switch (currentType) {
        case 'url':       return document.getElementById('qr-url')?.value||'';
        case 'text':      return document.getElementById('qr-text')?.value||'';
        case 'email':     { const e=document.getElementById('qr-email')?.value||''; const s=document.getElementById('qr-email-subject')?.value||''; const b=document.getElementById('qr-email-body')?.value||''; return e?`mailto:${e}?subject=${encodeURIComponent(s)}&body=${encodeURIComponent(b)}`:''; }
        case 'phone':     { const p=document.getElementById('qr-phone')?.value||''; return p?'tel:'+p:''; }
        case 'sms':       { const p=document.getElementById('qr-sms-phone')?.value||''; const m=document.getElementById('qr-sms-message')?.value||''; return p?`sms:${p}?body=${encodeURIComponent(m)}`:''; }
        case 'whatsapp':  { const p=(document.getElementById('qr-wa-phone')?.value||'').replace(/[^0-9]/g,''); const m=document.getElementById('qr-wa-message')?.value||''; return p?`https://wa.me/${p}${m?'?text='+encodeURIComponent(m):''}`:''; }
        case 'wifi':      { const s=document.getElementById('qr-wifi-ssid')?.value||''; const p=document.getElementById('qr-wifi-password')?.value||''; const e=document.getElementById('qr-wifi-encryption')?.value||'WPA'; const h=document.getElementById('qr-wifi-hidden')?.checked?'true':'false'; return s?`WIFI:T:${e};S:${s};P:${p};H:${h};;`:''; }
        case 'vcard':     { const fn=document.getElementById('qr-vc-fname')?.value||''; const ln=document.getElementById('qr-vc-lname')?.value||''; if(!fn&&!ln)return''; let v=`BEGIN:VCARD\nVERSION:3.0\nN:${ln};${fn}\nFN:${fn} ${ln}`; const o=document.getElementById('qr-vc-org')?.value;if(o)v+=`\nORG:${o}`; const t=document.getElementById('qr-vc-title')?.value;if(t)v+=`\nTITLE:${t}`; const p=document.getElementById('qr-vc-phone')?.value;if(p)v+=`\nTEL:${p}`; const e=document.getElementById('qr-vc-email')?.value;if(e)v+=`\nEMAIL:${e}`; const u=document.getElementById('qr-vc-url')?.value;if(u)v+=`\nURL:${u}`; const a=document.getElementById('qr-vc-address')?.value;if(a)v+=`\nADR:;;${a}`; v+='\nEND:VCARD'; return v; }
        case 'event':     { const t=document.getElementById('qr-ev-title')?.value||''; if(!t)return''; const st=(document.getElementById('qr-ev-start')?.value||'').replace(/[-:T]/g,'')+'00'; const en=(document.getElementById('qr-ev-end')?.value||'').replace(/[-:T]/g,'')+'00'; const l=document.getElementById('qr-ev-location')?.value||''; const d=document.getElementById('qr-ev-desc')?.value||''; return`BEGIN:VCALENDAR\nVERSION:2.0\nBEGIN:VEVENT\nSUMMARY:${t}\nDTSTART:${st}\nDTEND:${en}\nLOCATION:${l}\nDESCRIPTION:${d}\nEND:VEVENT\nEND:VCALENDAR`; }
        case 'geo':       { const la=document.getElementById('qr-geo-lat')?.value||''; const lo=document.getElementById('qr-geo-lng')?.value||''; const q=document.getElementById('qr-geo-query')?.value||''; return(la&&lo)?`geo:${la},${lo}${q?'?q='+encodeURIComponent(q):''}`:''; }
        case 'upi':       { const pa=document.getElementById('qr-upi-pa')?.value||''; if(!pa)return''; const pn=document.getElementById('qr-upi-pn')?.value||''; const am=document.getElementById('qr-upi-am')?.value||''; const tn=document.getElementById('qr-upi-tn')?.value||''; let u=`upi://pay?pa=${pa}&pn=${encodeURIComponent(pn)}`; if(am)u+=`&am=${am}`; if(tn)u+=`&tn=${encodeURIComponent(tn)}`; return u; }
        case 'bitcoin':   { const a=document.getElementById('qr-btc-addr')?.value||''; const am=document.getElementById('qr-btc-amount')?.value||''; return a?`bitcoin:${a}${am?'?amount='+am:''}`:''; }
        case 'youtube':   return document.getElementById('qr-yt-url')?.value||'';
        case 'twitter':   { const h=document.getElementById('qr-tw-handle')?.value||''; return h?`https://twitter.com/${h}`:''; }
        case 'instagram': { const h=document.getElementById('qr-ig-handle')?.value||''; return h?`https://instagram.com/${h}`:''; }
        case 'facebook':  { const u=document.getElementById('qr-fb-url')?.value||''; return u.startsWith('http')?u:(u?`https://facebook.com/${u}`:''); }
        case 'linkedin':  { const u=document.getElementById('qr-li-url')?.value||''; return u.startsWith('http')?u:(u?`https://linkedin.com/in/${u}`:''); }
        case 'spotify':   return document.getElementById('qr-sp-url')?.value||'';
        case 'zoom':      return document.getElementById('qr-zm-url')?.value||'';
        case 'pdf':       return document.getElementById('qr-pdf-url')?.value||'';
        case 'image':     return document.getElementById('qr-img-url')?.value||'';
        case 'mecard':    { const fn=document.getElementById('qr-mc-fname')?.value||''; const ln=document.getElementById('qr-mc-lname')?.value||''; if(!fn&&!ln)return''; let m=`MECARD:N:${ln},${fn};`; const p=document.getElementById('qr-mc-phone')?.value;if(p)m+=`TEL:${p};`; const e=document.getElementById('qr-mc-email')?.value;if(e)m+=`EMAIL:${e};`; m+=';'; return m; }
        case 'appstore':  return document.getElementById('qr-app-url')?.value||'';
        default:          return document.getElementById('qr-text')?.value||'';
    }
}

/* ── Helper: build gradient object for qr-code-styling ────────────── */
function buildGradient(typeId, startId, endId, angleId) {
    return {
        type: document.getElementById(typeId).value,
        rotation: parseInt(document.getElementById(angleId).value) * Math.PI / 180,
        colorStops: [
            { offset: 0, color: document.getElementById(startId).value },
            { offset: 1, color: document.getElementById(endId).value }
        ]
    };
}

/* ── getQROptions — reads ALL 62 controls ─────────────────────────── */
function getQROptions() {
    const fg      = document.getElementById('qr-fg-color').value;
    const bg      = document.getElementById('qr-bg-color').value;
    const ec      = document.getElementById('qr-ec').value;
    const dotType = document.getElementById('qr-dot-type').value;
    const cornerSq  = document.getElementById('qr-corner-square').value;
    const cornerDot = document.getElementById('qr-corner-dot').value;
    const margin    = parseInt(document.getElementById('qr-margin').value);
    const shape     = document.getElementById('qr-shape').value;

    // Sync hex displays
    document.getElementById('qr-fg-hex').value = fg;
    document.getElementById('qr-bg-hex').value = bg;

    const opts = {
        width: 300, height: 300, margin: margin,
        data: getQRContent(),
        type: shape === 'circle' ? 'canvas' : 'canvas',
        shape: shape === 'circle' ? 'circle' : 'square',
        qrOptions: { errorCorrectionLevel: ec },
        dotsOptions: { type: dotType, color: fg },
        backgroundOptions: { color: bg },
    };

    // Dot gradient
    if (document.getElementById('dot-grad-toggle').classList.contains('active')) {
        opts.dotsOptions.gradient = buildGradient('dot-grad-type', 'dot-grad-start', 'dot-grad-end', 'dot-grad-angle');
        delete opts.dotsOptions.color;
    }

    // Background transparent
    if (document.getElementById('bg-transparent-toggle').classList.contains('active')) {
        opts.backgroundOptions.color = 'transparent';
    }

    // Background gradient
    if (document.getElementById('bg-grad-toggle').classList.contains('active')) {
        opts.backgroundOptions.gradient = buildGradient('bg-grad-type', 'bg-grad-start', 'bg-grad-end', 'bg-grad-angle');
        if (!document.getElementById('bg-transparent-toggle').classList.contains('active')) {
            delete opts.backgroundOptions.color;
        }
    }

    // Rounded background
    if (document.getElementById('bg-rounded-toggle').classList.contains('active')) {
        opts.backgroundOptions.round = 0.2;
    }

    // Corner frame (corner squares)
    if (cornerSq) {
        opts.cornersSquareOptions = { type: cornerSq };
        if (document.getElementById('qr-corner-sq-custom').checked) {
            opts.cornersSquareOptions.color = document.getElementById('qr-corner-sq-color').value;
        } else {
            opts.cornersSquareOptions.color = fg;
        }
        // Corner square gradient
        if (document.getElementById('csq-grad-toggle').classList.contains('active')) {
            opts.cornersSquareOptions.gradient = buildGradient('csq-grad-type', 'csq-grad-start', 'csq-grad-end', 'csq-grad-angle');
            delete opts.cornersSquareOptions.color;
        }
    }

    // Corner dot
    if (cornerDot) {
        opts.cornersDotOptions = { type: cornerDot };
        if (document.getElementById('qr-corner-dot-custom').checked) {
            opts.cornersDotOptions.color = document.getElementById('qr-corner-dot-color').value;
        } else {
            opts.cornersDotOptions.color = fg;
        }
        // Corner dot gradient
        if (document.getElementById('cd-grad-toggle').classList.contains('active')) {
            opts.cornersDotOptions.gradient = buildGradient('cd-grad-type', 'cd-grad-start', 'cd-grad-end', 'cd-grad-angle');
            delete opts.cornersDotOptions.color;
        }
    }

    // Logo
    if (logoDataUrl) {
        const hideDots = document.getElementById('logo-hide-dots-toggle').classList.contains('active');
        opts.image = logoDataUrl;
        opts.imageOptions = {
            hideBackgroundDots: hideDots,
            imageSize: parseInt(document.getElementById('qr-logo-size').value) / 100,
            margin: parseInt(document.getElementById('qr-logo-margin').value),
        };
    }

    return opts;
}

/* ── Debounced QR update ──────────────────────────────────────────── */
function updateQR() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(_doUpdateQR, 160);
}

function _doUpdateQR() {
    const content     = getQRContent();
    const container   = document.getElementById('qr-code');
    const placeholder = document.getElementById('qr-placeholder');
    const wrapper     = document.getElementById('qr-wrapper');
    const bg          = document.getElementById('qr-bg-color').value;

    const btns = ['btn-download-png','btn-download-svg','btn-save','btn-print','btn-copy','btn-pdf','btn-download-big'];

    if (!content) {
        container.innerHTML = '';
        container.style.display = 'none';
        placeholder.style.display = 'flex';
        wrapper.classList.remove('has-qr');
        btns.forEach(b => { const el = document.getElementById(b); if (el) el.disabled = true; });
        qrInstance = null;
        return;
    }

    container.style.display = 'block';
    placeholder.style.display = 'none';
    wrapper.classList.add('has-qr');
    container.innerHTML = '';

    const isTrans = document.getElementById('bg-transparent-toggle').classList.contains('active');
    container.style.backgroundColor = isTrans ? 'transparent' : bg;

    btns.forEach(b => { const el = document.getElementById(b); if (el) el.disabled = false; });

    const opts = getQROptions();
    qrInstance = new QRCodeStyling(opts);
    qrInstance.append(container);

    // Apply live frame/shadow CSS preview
    applyFrameShadowCSS();
}

/* ── Frame & Shadow live CSS preview ──────────────────────────────── */
function applyFrameShadowCSS() {
    const wrapper = document.getElementById('qr-wrapper');
    if (!wrapper) return;

    const frameOn  = document.getElementById('frame-enable-toggle').classList.contains('active');
    const shadowOn = document.getElementById('shadow-enable-toggle').classList.contains('active');

    // Update hidden fields
    document.getElementById('frame-enable').value  = frameOn ? '1' : '0';
    document.getElementById('shadow-enable').value = shadowOn ? '1' : '0';

    // Show/hide option panels
    document.getElementById('frame-opts').style.display  = frameOn ? 'block' : 'none';
    document.getElementById('shadow-opts').style.display = shadowOn ? 'block' : 'none';

    // Frame CSS
    if (frameOn) {
        const fw = document.getElementById('frame-width').value + 'px';
        const fp = document.getElementById('frame-padding').value + 'px';
        const fr = document.getElementById('frame-radius').value + 'px';
        const fc = document.getElementById('frame-color').value;
        wrapper.style.border = fw + ' solid ' + fc;
        wrapper.style.padding = fp;
        wrapper.style.borderRadius = fr;
    } else {
        wrapper.style.border = '1.5px solid var(--border)';
        wrapper.style.padding = '20px';
        wrapper.style.borderRadius = '18px';
    }

    // Shadow CSS
    if (shadowOn) {
        const sc = document.getElementById('shadow-color').value;
        const sb = document.getElementById('shadow-blur').value;
        const sox = document.getElementById('shadow-ox').value;
        const soy = document.getElementById('shadow-oy').value;
        // Convert hex to rgba for shadow
        const r = parseInt(sc.slice(1,3),16), g = parseInt(sc.slice(3,5),16), b = parseInt(sc.slice(5,7),16);
        wrapper.style.boxShadow = `${sox}px ${soy}px ${sb}px rgba(${r},${g},${b},0.35)`;
    } else {
        wrapper.style.boxShadow = wrapper.classList.contains('has-qr') ? '0 6px 32px rgba(0,0,0,.1)' : 'none';
    }

    // Label preview: show small text below QR
    const labelText = document.getElementById('frame-label')?.value || '';
    let labelEl = document.getElementById('qr-frame-label-preview');
    if (frameOn && labelText) {
        if (!labelEl) {
            labelEl = document.createElement('div');
            labelEl.id = 'qr-frame-label-preview';
            labelEl.style.cssText = 'text-align:center;font-weight:700;font-family:"Plus Jakarta Sans",sans-serif;margin-top:8px;';
            wrapper.appendChild(labelEl);
        }
        labelEl.textContent = labelText;
        labelEl.style.color = document.getElementById('frame-label-color').value;
        labelEl.style.fontSize = document.getElementById('frame-label-size').value + 'px';
    } else if (labelEl) {
        labelEl.remove();
    }
}

/* ── Canvas compositing for export (frame + shadow + label) ───────── */
function roundRect(ctx, x, y, w, h, r) {
    r = Math.min(r, w / 2, h / 2);
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.lineTo(x + w - r, y);
    ctx.quadraticCurveTo(x + w, y, x + w, y + r);
    ctx.lineTo(x + w, y + h - r);
    ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
    ctx.lineTo(x + r, y + h);
    ctx.quadraticCurveTo(x, y + h, x, y + h - r);
    ctx.lineTo(x, y + r);
    ctx.quadraticCurveTo(x, y, x + r, y);
    ctx.closePath();
}

function compositeExport(qrCanvas) {
    const frameEnabled  = document.getElementById('frame-enable-toggle').classList.contains('active');
    const shadowEnabled = document.getElementById('shadow-enable-toggle').classList.contains('active');
    const labelText     = document.getElementById('frame-label')?.value || '';

    if (!frameEnabled && !shadowEnabled && !labelText) return qrCanvas;

    const frameWidth   = frameEnabled ? parseInt(document.getElementById('frame-width').value) : 0;
    const framePadding = frameEnabled ? parseInt(document.getElementById('frame-padding').value) : 0;
    const labelHeight  = (frameEnabled && labelText) ? parseInt(document.getElementById('frame-label-size').value) + 20 : 0;
    const shadowBlur   = shadowEnabled ? parseInt(document.getElementById('shadow-blur').value) : 0;
    const totalPadding = frameWidth + framePadding + shadowBlur + 10;

    const w = qrCanvas.width + totalPadding * 2;
    const h = qrCanvas.height + totalPadding * 2 + labelHeight;

    const c = document.createElement('canvas');
    c.width = w; c.height = h;
    const ctx = c.getContext('2d');

    // Transparent bg first
    ctx.clearRect(0, 0, w, h);

    // Shadow
    if (shadowEnabled) {
        ctx.shadowColor = document.getElementById('shadow-color').value + '59';
        ctx.shadowBlur = shadowBlur;
        ctx.shadowOffsetX = parseInt(document.getElementById('shadow-ox').value);
        ctx.shadowOffsetY = parseInt(document.getElementById('shadow-oy').value);
    }

    // Frame background rect
    if (frameEnabled) {
        const radius = parseInt(document.getElementById('frame-radius').value);
        ctx.fillStyle = document.getElementById('frame-color').value;
        roundRect(ctx,
            totalPadding - frameWidth - framePadding,
            totalPadding - frameWidth - framePadding,
            qrCanvas.width + (frameWidth + framePadding) * 2,
            qrCanvas.height + (frameWidth + framePadding) * 2 + labelHeight,
            radius);
        ctx.fill();
    }

    // Reset shadow
    ctx.shadowColor = 'transparent';
    ctx.shadowBlur = 0;
    ctx.shadowOffsetX = 0;
    ctx.shadowOffsetY = 0;

    // Draw QR
    ctx.drawImage(qrCanvas, totalPadding, totalPadding);

    // Label
    if (frameEnabled && labelText) {
        const labelSize = parseInt(document.getElementById('frame-label-size').value);
        ctx.font = `bold ${labelSize}px 'Plus Jakarta Sans', sans-serif`;
        ctx.fillStyle = document.getElementById('frame-label-color').value;
        ctx.textAlign = 'center';
        ctx.fillText(labelText, w / 2, totalPadding + qrCanvas.height + framePadding + labelSize + 5);
    }

    return c;
}

/* ── Download (PNG/SVG) with compositing ──────────────────────────── */
function downloadQR(fmt) {
    if (!qrInstance) return;
    const s = parseInt(document.getElementById('qr-size').value);
    const opts = getQROptions();
    opts.width = s; opts.height = s;

    const exportQR = new QRCodeStyling(opts);
    const tmp = document.createElement('div');
    tmp.style.cssText = 'position:absolute;left:-9999px';
    document.body.appendChild(tmp);
    exportQR.append(tmp);

    setTimeout(() => {
        const fname = `qrcode_${currentType}_${Date.now()}`;

        if (fmt === 'svg') {
            exportQR.download({ name: fname, extension: 'svg' });
            document.body.removeChild(tmp);
            return;
        }

        // PNG with compositing
        const cv = tmp.querySelector('canvas');
        if (!cv) { exportQR.download({ name: fname, extension: 'png' }); document.body.removeChild(tmp); return; }

        const finalCanvas = compositeExport(cv);
        const link = document.createElement('a');
        link.download = fname + '.png';
        link.href = finalCanvas.toDataURL('image/png');
        link.click();
        document.body.removeChild(tmp);
    }, 350);
}

/* ── Logo handling ────────────────────────────────────────────────── */
function handleLogo(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => { logoDataUrl = e.target.result; updateQR(); };
    reader.readAsDataURL(file);
}

function clearLogo() {
    logoDataUrl = '';
    document.getElementById('qr-logo-file').value = '';
    updateQR();
}

/* ── Save to database ─────────────────────────────────────────────── */
function saveQRToDB() {
    const c = getQRContent(); if (!c) return;
    document.getElementById('form-qr-title').value   = document.getElementById('qr-save-title').value || '<?= addslashes($types[$selectedType]['name']) ?>';
    document.getElementById('form-qr-content').value = c;
    document.getElementById('form-fg-color').value   = document.getElementById('qr-fg-color').value;
    document.getElementById('form-bg-color').value   = document.getElementById('qr-bg-color').value;
    document.getElementById('form-qr-size').value    = document.getElementById('qr-size').value;
    document.getElementById('form-qr-ec').value      = document.getElementById('qr-ec').value;
    document.getElementById('save-form').submit();
}

/* ── Print QR ─────────────────────────────────────────────────────── */
function printQR() {
    const cv = document.querySelector('#qr-code canvas'); if (!cv) return;
    const finalCanvas = compositeExport(cv);
    const w = window.open('','_blank');
    w.document.write(`<html><head><title>QR Code - ${currentType}</title></head><body style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#fff;font-family:sans-serif;"><h2 style="margin-bottom:20px;color:#333">${document.getElementById('qr-save-title').value||currentType+' QR Code'}</h2><img src="${finalCanvas.toDataURL()}" style="max-width:400px;"><p style="margin-top:16px;color:#999;font-size:12px">Generated by QRCode Pro</p></body></html>`);
    w.document.close(); w.onload = () => { w.print(); };
}

/* ── Copy to clipboard ────────────────────────────────────────────── */
function copyQR() {
    if (!qrInstance) return;
    const cv = document.querySelector('#qr-code canvas');
    if (!cv) return;
    const finalCanvas = compositeExport(cv);
    finalCanvas.toBlob(blob => {
        if (!blob) return;
        navigator.clipboard.write([new ClipboardItem({'image/png': blob})]).then(() => {
            const btn = document.getElementById('btn-copy');
            const orig = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="check" style="width:16px;height:16px"></i> Copied!';
            lucide.createIcons();
            setTimeout(() => { btn.innerHTML = orig; lucide.createIcons(); }, 2000);
        });
    }, 'image/png');
}

/* ── PDF export (jsPDF) with compositing ──────────────────────────── */
function downloadPDF() {
    if (!qrInstance) return;
    const s = parseInt(document.getElementById('qr-size').value);
    const opts = getQROptions();
    opts.width = s; opts.height = s;
    const exportQR = new QRCodeStyling(opts);
    const tmp = document.createElement('div');
    tmp.style.cssText = 'position:absolute;left:-9999px';
    document.body.appendChild(tmp);
    exportQR.append(tmp);
    setTimeout(() => {
        const cv = tmp.querySelector('canvas');
        if (!cv) { document.body.removeChild(tmp); return; }
        const finalCanvas = compositeExport(cv);
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const title = document.getElementById('qr-save-title').value || currentType + ' QR Code';
        doc.setFont('helvetica','bold'); doc.setFontSize(20);
        doc.text(title, 105, 30, {align:'center'});
        doc.setFont('helvetica','normal'); doc.setFontSize(10);
        doc.text('Generated by QRCode Pro', 105, 38, {align:'center'});
        const imgData = finalCanvas.toDataURL('image/png');
        const qrSize = 100;
        doc.addImage(imgData, 'PNG', (210-qrSize)/2, 50, qrSize, qrSize);
        doc.setFontSize(8); doc.setTextColor(150);
        doc.text('QRCode Pro - ' + new Date().toLocaleDateString(), 105, 160, {align:'center'});
        doc.save('qrcode_' + currentType + '_' + Date.now() + '.pdf');
        document.body.removeChild(tmp);
    }, 400);
}

/* ── Color preset ─────────────────────────────────────────────────── */
function applyPreset(el) {
    document.querySelectorAll('.color-preset').forEach(p => p.classList.remove('active-preset'));
    el.classList.add('active-preset');
    const fg = el.getAttribute('data-fg');
    const bg = el.getAttribute('data-bg');
    document.getElementById('qr-fg-color').value = fg;
    document.getElementById('qr-fg-hex').value = fg;
    document.getElementById('qr-bg-color').value = bg;
    document.getElementById('qr-bg-hex').value = bg;
    updateQR();
}

/* ── Accordion toggle ─────────────────────────────────────────────── */
function toggleCust(head) {
    const body = head.nextElementSibling;
    const chev = head.querySelector('.chev');
    const isOpen = body.style.maxHeight !== '0px' && body.style.maxHeight !== '' && body.style.maxHeight !== '0';
    if (isOpen) {
        body.style.maxHeight = body.scrollHeight + 'px';
        body.offsetHeight;
        body.style.maxHeight = '0px';
        body.style.overflow = 'hidden';
    } else {
        body.style.overflow = 'hidden';
        body.style.maxHeight = body.scrollHeight + 100 + 'px';
        setTimeout(() => { body.style.maxHeight = '3000px'; body.style.overflow = 'visible'; }, 380);
    }
    chev.classList.toggle('open', !isOpen);
}

/* ── Toggle switch (custom checkbox) ──────────────────────────────── */
function toggleSwitch(el) {
    el.classList.toggle('active');
}

/* ── Show/hide gradient options ───────────────────────────────────── */
function toggleGradient(containerId, show) {
    const el = document.getElementById(containerId);
    if (!el) return;
    if (show) {
        el.classList.add('show');
    } else {
        el.classList.remove('show');
    }
}

/* ── Initialise ───────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    // Sync slider display values on load
    document.querySelectorAll('.slider-row input[type=range]').forEach(slider => {
        const valSpan = slider.closest('.slider-row')?.querySelector('.slider-val');
        if (valSpan && slider.id) {
            slider.addEventListener('input', () => {
                let unit = 'px';
                if (slider.id.includes('angle')) unit = '\u00B0';
                else if (slider.id === 'qr-logo-size') unit = '%';
                else if (slider.id === 'qr-logo-opacity') unit = '';
                valSpan.textContent = slider.value + unit;
            });
        }
    });
    // ═══════════════════════════════════════════════════════════════════
    // AUTO-UPGRADE: Custom Select Dropdowns (runs inside DOMContentLoaded)
    // ═══════════════════════════════════════════════════════════════════
    document.querySelectorAll('select.input-field').forEach(sel => {
    const wrap = document.createElement('div');
    wrap.className = 'cs-wrap';
    sel.parentNode.insertBefore(wrap, sel);
    wrap.appendChild(sel);
    sel.style.display = 'none';

    const trigger = document.createElement('div');
    trigger.className = 'cs-trigger';
    const selectedOpt = sel.options[sel.selectedIndex];
    trigger.innerHTML = '<span class="cs-text">' + (selectedOpt ? selectedOpt.text : '') + '</span><svg class="cs-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>';
    wrap.insertBefore(trigger, sel);

    const dropdown = document.createElement('div');
    dropdown.className = 'cs-dropdown';
    Array.from(sel.options).forEach(opt => {
        const div = document.createElement('div');
        div.className = 'cs-option' + (opt.selected ? ' selected' : '');
        div.dataset.value = opt.value;
        div.innerHTML = opt.text + '<svg class="cs-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
        div.addEventListener('click', () => {
            sel.value = opt.value;
            trigger.querySelector('.cs-text').textContent = opt.text;
            dropdown.querySelectorAll('.cs-option').forEach(o => o.classList.remove('selected'));
            div.classList.add('selected');
            dropdown.classList.remove('show');
            trigger.classList.remove('open');
            sel.dispatchEvent(new Event('change'));
            updateQR();
        });
        dropdown.appendChild(div);
    });
    wrap.appendChild(dropdown);

    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = dropdown.classList.contains('show');
        document.querySelectorAll('.cs-dropdown.show').forEach(d => { d.classList.remove('show'); d.parentElement.querySelector('.cs-trigger')?.classList.remove('open'); });
        document.querySelectorAll('.cp-panel.show').forEach(p => p.classList.remove('show'));
        if (!isOpen) { dropdown.classList.add('show'); trigger.classList.add('open'); }
    });
    }); // ← closes select.forEach

    // ═══════════════════════════════════════════════════════════════════
    // AUTO-UPGRADE: Custom Color Pickers (runs inside DOMContentLoaded)
    // ═══════════════════════════════════════════════════════════════════
const CP_SWATCHES = [
    '#000000','#1E293B','#334155','#475569','#64748B','#94A3B8','#CBD5E1','#FFFFFF',
    '#EF4444','#F97316','#F59E0B','#EAB308','#84CC16','#22C55E','#10B981','#14B8A6',
    '#06B6D4','#0EA5E9','#3B82F6','#6366F1','#8B5CF6','#A855F7','#D946EF','#EC4899',
    '#B91C1C','#C2410C','#B45309','#A16207','#4D7C0F','#15803D','#0F766E','#0E7490',
    '#1D4ED8','#4338CA','#6D28D9','#7E22CE','#A21CAF','#BE185D','#9F1239','#713F12',
];

document.querySelectorAll('input[type=color]').forEach(inp => {
    const wrap = document.createElement('div');
    wrap.className = 'cp-wrap';
    inp.parentNode.insertBefore(wrap, inp);
    wrap.appendChild(inp);
    inp.style.display = 'none';

    // Also hide any adjacent hex text input
    const hexInput = inp.nextElementSibling;
    const hasHex = hexInput && hexInput.type === 'text' && hexInput.maxLength === 7;
    if (hasHex) { wrap.appendChild(hexInput); hexInput.style.display = 'none'; }

    const trigger = document.createElement('div');
    trigger.className = 'cp-trigger';
    trigger.innerHTML = '<div class="cp-swatch" style="background:' + inp.value + '"></div><span class="cp-hex">' + inp.value.toUpperCase() + '</span><span class="cp-label">click</span>';
    wrap.insertBefore(trigger, inp);

    const panel = document.createElement('div');
    panel.className = 'cp-panel';
    let html = '<div class="cp-panel-title">Pick a Color</div><div class="cp-grid">';
    CP_SWATCHES.forEach(c => {
        html += '<div class="cp-color' + (c.toUpperCase() === inp.value.toUpperCase() ? ' active' : '') + '" style="background:' + c + '" data-color="' + c + '"></div>';
    });
    html += '</div><div class="cp-input-row"><input type="color" class="cp-native" value="' + inp.value + '"><input type="text" class="cp-hex-input" value="' + inp.value + '" maxlength="7" placeholder="#000000"></div>';
    panel.innerHTML = html;
    wrap.appendChild(panel);

    function setColor(color) {
        inp.value = color;
        if (hasHex) hexInput.value = color;
        trigger.querySelector('.cp-swatch').style.background = color;
        trigger.querySelector('.cp-hex').textContent = color.toUpperCase();
        panel.querySelector('.cp-native').value = color;
        panel.querySelector('.cp-hex-input').value = color;
        panel.querySelectorAll('.cp-color').forEach(s => s.classList.toggle('active', s.dataset.color.toUpperCase() === color.toUpperCase()));
        inp.dispatchEvent(new Event('change'));
        updateQR();
    }

    panel.querySelectorAll('.cp-color').forEach(swatch => {
        swatch.addEventListener('click', () => setColor(swatch.dataset.color));
    });

    panel.querySelector('.cp-native').addEventListener('input', function() { setColor(this.value); });
    panel.querySelector('.cp-hex-input').addEventListener('change', function() {
        const v = this.value.trim();
        if (/^#[0-9a-fA-F]{6}$/.test(v)) setColor(v);
    });

    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = panel.classList.contains('show');
        document.querySelectorAll('.cp-panel.show').forEach(p => p.classList.remove('show'));
        document.querySelectorAll('.cs-dropdown.show').forEach(d => { d.classList.remove('show'); d.parentElement.querySelector('.cs-trigger')?.classList.remove('open'); });
        if (!isOpen) panel.classList.add('show');
    });
});

// Close all custom dropdowns/pickers when clicking outside
document.addEventListener('click', () => {
    document.querySelectorAll('.cs-dropdown.show').forEach(d => { d.classList.remove('show'); d.parentElement.querySelector('.cs-trigger')?.classList.remove('open'); });
    document.querySelectorAll('.cp-panel.show').forEach(p => p.classList.remove('show'));
});
// Prevent panel clicks from closing
document.querySelectorAll('.cp-panel, .cs-dropdown').forEach(el => el.addEventListener('click', e => e.stopPropagation()));
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
