<?php
$pageTitle = 'Generate QR Code';
require_once __DIR__ . '/includes/header.php';
requireLogin();

$types = getQRTypes();
$selectedType = $_GET['type'] ?? 'url';
if (!isset($types[$selectedType]) || $selectedType === 'bulk') $selectedType = 'url';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_qr'])) {
    $type = $_POST['qr_type'] ?? 'url';
    $title = trim($_POST['qr_title'] ?? '');
    $content = $_POST['qr_content'] ?? '';
    $filename = generateFilename($type);
    $settings = [
        'fg_color' => $_POST['fg_color'] ?? '#000000',
        'bg_color' => $_POST['bg_color'] ?? '#ffffff',
        'size' => $_POST['qr_size'] ?? 256,
        'error_correction' => $_POST['error_correction'] ?? 'H'
    ];
    if ($currentUser) {
        saveQRCode($currentUser['id'], $type, $title ?: ($types[$type]['name'] ?? $type), $content, $filename, $settings);
        setFlash('success', 'QR Code saved to your history!');
    }
    header('Location: ' . url('/history'));
    exit;
}
$B = BASE_PATH;
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="flex items-center justify-between mb-8 animate-fade-in-up">
        <div><h1 class="font-display text-3xl font-bold mb-1 text-slate-900">Generate QR Code</h1>
        <p class="text-slate-500">Choose a type and customize your QR code</p></div>
    </div>

    <div class="grid lg:grid-cols-12 gap-8">
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-soft animate-fade-in-up delay-100">
                <h3 class="font-display text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Select QR Type</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($types as $key => $type): if ($key === 'bulk') continue; ?>
                    <a href="<?= url('/generate?type=' . $key) ?>" 
                       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium transition-all
                              <?= $key === $selectedType ? 'bg-brand-50 ring-1 ring-brand-200 text-brand-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' ?>">
                        <i data-lucide="<?= $type['icon'] ?>" class="w-4 h-4" style="color: <?= $type['color'] ?>"></i>
                        <span class="hidden sm:inline"><?= $type['name'] ?></span></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-soft animate-fade-in-up delay-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: <?= $types[$selectedType]['color'] ?>12;">
                        <i data-lucide="<?= $types[$selectedType]['icon'] ?>" class="w-5 h-5" style="color: <?= $types[$selectedType]['color'] ?>"></i></div>
                    <div><h2 class="font-display text-xl font-bold text-slate-800"><?= $types[$selectedType]['name'] ?></h2>
                    <p class="text-sm text-slate-500"><?= $types[$selectedType]['desc'] ?></p></div>
                </div>
                <div id="qr-form-fields" class="space-y-4">
                    <?php switch ($selectedType):
                        case 'url': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Website URL</label><input type="url" id="qr-url" class="w-full px-4 py-3 input-field" placeholder="https://example.com" oninput="updateQR()"></div>
                        <?php break; case 'text': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Text Content</label><textarea id="qr-text" rows="4" class="w-full px-4 py-3 input-field" placeholder="Enter your text here..." oninput="updateQR()"></textarea></div>
                        <?php break; case 'email': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Email Address</label><input type="email" id="qr-email" class="w-full px-4 py-3 input-field" placeholder="hello@example.com" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Subject</label><input type="text" id="qr-email-subject" class="w-full px-4 py-3 input-field" placeholder="Email subject" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Body</label><textarea id="qr-email-body" rows="3" class="w-full px-4 py-3 input-field" placeholder="Email body" oninput="updateQR()"></textarea></div>
                        <?php break; case 'phone': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Phone Number</label><input type="tel" id="qr-phone" class="w-full px-4 py-3 input-field" placeholder="+1234567890" oninput="updateQR()"></div>
                        <?php break; case 'sms': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Phone Number</label><input type="tel" id="qr-sms-phone" class="w-full px-4 py-3 input-field" placeholder="+1234567890" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Message</label><textarea id="qr-sms-message" rows="3" class="w-full px-4 py-3 input-field" placeholder="Your SMS message" oninput="updateQR()"></textarea></div>
                        <?php break; case 'whatsapp': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Phone (with country code)</label><input type="tel" id="qr-wa-phone" class="w-full px-4 py-3 input-field" placeholder="911234567890" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Pre-filled Message (optional)</label><textarea id="qr-wa-message" rows="3" class="w-full px-4 py-3 input-field" placeholder="Hello!" oninput="updateQR()"></textarea></div>
                        <?php break; case 'wifi': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Network Name (SSID)</label><input type="text" id="qr-wifi-ssid" class="w-full px-4 py-3 input-field" placeholder="MyWiFiNetwork" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Password</label><input type="text" id="qr-wifi-password" class="w-full px-4 py-3 input-field" placeholder="WiFi password" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Encryption</label><select id="qr-wifi-encryption" class="w-full px-4 py-3 input-field" onchange="updateQR()"><option value="WPA">WPA/WPA2</option><option value="WEP">WEP</option><option value="nopass">No Password</option></select></div><div class="flex items-center gap-3"><input type="checkbox" id="qr-wifi-hidden" class="w-4 h-4 rounded text-brand-500" onchange="updateQR()"><label for="qr-wifi-hidden" class="text-sm text-slate-600">Hidden Network</label></div>
                        <?php break; case 'vcard': ?><div class="grid sm:grid-cols-2 gap-4"><div><label class="block text-sm font-semibold text-slate-600 mb-2">First Name</label><input type="text" id="qr-vc-fname" class="w-full px-4 py-3 input-field" placeholder="John" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Last Name</label><input type="text" id="qr-vc-lname" class="w-full px-4 py-3 input-field" placeholder="Doe" oninput="updateQR()"></div></div><div class="grid sm:grid-cols-2 gap-4"><div><label class="block text-sm font-semibold text-slate-600 mb-2">Organization</label><input type="text" id="qr-vc-org" class="w-full px-4 py-3 input-field" placeholder="Company" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Job Title</label><input type="text" id="qr-vc-title" class="w-full px-4 py-3 input-field" placeholder="CEO" oninput="updateQR()"></div></div><div class="grid sm:grid-cols-2 gap-4"><div><label class="block text-sm font-semibold text-slate-600 mb-2">Phone</label><input type="tel" id="qr-vc-phone" class="w-full px-4 py-3 input-field" placeholder="+1234567890" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Email</label><input type="email" id="qr-vc-email" class="w-full px-4 py-3 input-field" placeholder="john@co.com" oninput="updateQR()"></div></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Website</label><input type="url" id="qr-vc-url" class="w-full px-4 py-3 input-field" placeholder="https://company.com" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Address</label><input type="text" id="qr-vc-address" class="w-full px-4 py-3 input-field" placeholder="123 Main St" oninput="updateQR()"></div>
                        <?php break; case 'event': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Event Title</label><input type="text" id="qr-ev-title" class="w-full px-4 py-3 input-field" placeholder="Team Meeting" oninput="updateQR()"></div><div class="grid sm:grid-cols-2 gap-4"><div><label class="block text-sm font-semibold text-slate-600 mb-2">Start</label><input type="datetime-local" id="qr-ev-start" class="w-full px-4 py-3 input-field" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">End</label><input type="datetime-local" id="qr-ev-end" class="w-full px-4 py-3 input-field" oninput="updateQR()"></div></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Location</label><input type="text" id="qr-ev-location" class="w-full px-4 py-3 input-field" placeholder="Room A" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Description</label><textarea id="qr-ev-desc" rows="2" class="w-full px-4 py-3 input-field" placeholder="Details..." oninput="updateQR()"></textarea></div>
                        <?php break; case 'geo': ?><div class="grid sm:grid-cols-2 gap-4"><div><label class="block text-sm font-semibold text-slate-600 mb-2">Latitude</label><input type="number" step="any" id="qr-geo-lat" class="w-full px-4 py-3 input-field" placeholder="23.0225" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Longitude</label><input type="number" step="any" id="qr-geo-lng" class="w-full px-4 py-3 input-field" placeholder="72.5714" oninput="updateQR()"></div></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Location Name (optional)</label><input type="text" id="qr-geo-query" class="w-full px-4 py-3 input-field" placeholder="Sabarmati Ashram" oninput="updateQR()"></div>
                        <?php break; case 'upi': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">UPI ID</label><input type="text" id="qr-upi-pa" class="w-full px-4 py-3 input-field" placeholder="name@upi" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Payee Name</label><input type="text" id="qr-upi-pn" class="w-full px-4 py-3 input-field" placeholder="John Doe" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Amount (₹)</label><input type="number" step="0.01" id="qr-upi-am" class="w-full px-4 py-3 input-field" placeholder="100.00" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Note</label><input type="text" id="qr-upi-tn" class="w-full px-4 py-3 input-field" placeholder="Payment for..." oninput="updateQR()"></div>
                        <?php break; case 'bitcoin': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Bitcoin Address</label><input type="text" id="qr-btc-addr" class="w-full px-4 py-3 input-field" placeholder="1A1zP1..." oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Amount (BTC)</label><input type="number" step="0.00000001" id="qr-btc-amount" class="w-full px-4 py-3 input-field" placeholder="0.001" oninput="updateQR()"></div>
                        <?php break; case 'youtube': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">YouTube URL or Video ID</label><input type="text" id="qr-yt-url" class="w-full px-4 py-3 input-field" placeholder="https://youtube.com/watch?v=..." oninput="updateQR()"></div>
                        <?php break; case 'twitter': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Twitter/X Handle</label><div class="relative"><span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">@</span><input type="text" id="qr-tw-handle" class="w-full pl-8 pr-4 py-3 input-field" placeholder="username" oninput="updateQR()"></div></div>
                        <?php break; case 'instagram': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Instagram Handle</label><div class="relative"><span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">@</span><input type="text" id="qr-ig-handle" class="w-full pl-8 pr-4 py-3 input-field" placeholder="username" oninput="updateQR()"></div></div>
                        <?php break; case 'facebook': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Facebook URL or Username</label><input type="text" id="qr-fb-url" class="w-full px-4 py-3 input-field" placeholder="https://facebook.com/yourpage" oninput="updateQR()"></div>
                        <?php break; case 'linkedin': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">LinkedIn Profile URL</label><input type="text" id="qr-li-url" class="w-full px-4 py-3 input-field" placeholder="https://linkedin.com/in/username" oninput="updateQR()"></div>
                        <?php break; case 'spotify': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Spotify URL</label><input type="text" id="qr-sp-url" class="w-full px-4 py-3 input-field" placeholder="https://open.spotify.com/track/..." oninput="updateQR()"></div>
                        <?php break; case 'zoom': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Zoom Meeting URL</label><input type="text" id="qr-zm-url" class="w-full px-4 py-3 input-field" placeholder="https://zoom.us/j/..." oninput="updateQR()"></div>
                        <?php break; case 'pdf': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">PDF File URL</label><input type="url" id="qr-pdf-url" class="w-full px-4 py-3 input-field" placeholder="https://example.com/doc.pdf" oninput="updateQR()"></div><p class="text-xs text-slate-400">Upload your PDF to a cloud service and paste the link here.</p>
                        <?php break; case 'image': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">Image URL</label><input type="url" id="qr-img-url" class="w-full px-4 py-3 input-field" placeholder="https://example.com/image.jpg" oninput="updateQR()"></div>
                        <?php break; case 'mecard': ?><div class="grid sm:grid-cols-2 gap-4"><div><label class="block text-sm font-semibold text-slate-600 mb-2">First Name</label><input type="text" id="qr-mc-fname" class="w-full px-4 py-3 input-field" placeholder="John" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Last Name</label><input type="text" id="qr-mc-lname" class="w-full px-4 py-3 input-field" placeholder="Doe" oninput="updateQR()"></div></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Phone</label><input type="tel" id="qr-mc-phone" class="w-full px-4 py-3 input-field" placeholder="+1234567890" oninput="updateQR()"></div><div><label class="block text-sm font-semibold text-slate-600 mb-2">Email</label><input type="email" id="qr-mc-email" class="w-full px-4 py-3 input-field" placeholder="john@example.com" oninput="updateQR()"></div>
                        <?php break; case 'appstore': ?><div><label class="block text-sm font-semibold text-slate-600 mb-2">App Store / Play Store URL</label><input type="url" id="qr-app-url" class="w-full px-4 py-3 input-field" placeholder="https://apps.apple.com/..." oninput="updateQR()"></div>
                    <?php break; endswitch; ?>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-soft animate-fade-in-up delay-300">
                <h3 class="font-display text-lg font-bold mb-4 flex items-center gap-2 text-slate-800"><i data-lucide="palette" class="w-5 h-5 text-lilac-500"></i> Customization</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-semibold text-slate-600 mb-2">Foreground Color</label><div class="flex items-center gap-3"><input type="color" id="qr-fg-color" value="#000000" onchange="updateQR()" class="w-10 h-10 rounded-lg cursor-pointer border border-slate-200 bg-white p-0.5"><input type="text" id="qr-fg-hex" value="#000000" class="flex-1 px-4 py-3 input-field font-mono text-sm" oninput="document.getElementById('qr-fg-color').value=this.value;updateQR()"></div></div>
                    <div><label class="block text-sm font-semibold text-slate-600 mb-2">Background Color</label><div class="flex items-center gap-3"><input type="color" id="qr-bg-color" value="#ffffff" onchange="updateQR()" class="w-10 h-10 rounded-lg cursor-pointer border border-slate-200 bg-white p-0.5"><input type="text" id="qr-bg-hex" value="#ffffff" class="flex-1 px-4 py-3 input-field font-mono text-sm" oninput="document.getElementById('qr-bg-color').value=this.value;updateQR()"></div></div>
                    <div><label class="block text-sm font-semibold text-slate-600 mb-2">Size</label><select id="qr-size" class="w-full px-4 py-3 input-field" onchange="updateQR()"><option value="128">128×128</option><option value="256" selected>256×256</option><option value="512">512×512</option><option value="1024">1024×1024</option></select></div>
                    <div><label class="block text-sm font-semibold text-slate-600 mb-2">Error Correction</label><select id="qr-ec" class="w-full px-4 py-3 input-field" onchange="updateQR()"><option value="L">Low (7%)</option><option value="M">Medium (15%)</option><option value="Q">Quartile (25%)</option><option value="H" selected>High (30%)</option></select></div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="sticky top-24 space-y-6">
                <div class="bg-white rounded-2xl p-8 text-center border border-slate-100 shadow-soft animate-scale-in">
                    <h3 class="font-display text-xs font-bold text-slate-400 uppercase tracking-wider mb-6">Live Preview</h3>
                    <div id="qr-preview-container" class="inline-block"><div id="qr-code" class="qr-preview mx-auto" style="display:none;"></div></div>
                    <div id="qr-placeholder" class="py-12"><div class="w-20 h-20 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-4"><i data-lucide="qr-code" class="w-10 h-10 text-slate-300"></i></div><p class="text-slate-400 text-sm">Fill in the form to preview</p></div>
                    <div class="mt-6"><input type="text" id="qr-save-title" class="w-full px-4 py-3 input-field text-center" placeholder="Give your QR code a name..."></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button onclick="downloadQR('png')" id="btn-download-png" class="flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl font-semibold btn-primary disabled:opacity-30" disabled><i data-lucide="download" class="w-4 h-4"></i> PNG</button>
                    <button onclick="downloadQR('svg')" id="btn-download-svg" class="flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl font-semibold bg-white border-2 border-lilac-200 text-lilac-600 hover:bg-lilac-50 transition-all disabled:opacity-30" disabled><i data-lucide="download" class="w-4 h-4"></i> SVG</button>
                    <button onclick="saveQRToDB()" id="btn-save" class="flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl font-semibold bg-white border-2 border-brand-200 text-brand-600 hover:bg-brand-50 transition-all disabled:opacity-30" disabled><i data-lucide="save" class="w-4 h-4"></i> Save</button>
                    <button onclick="printQR()" id="btn-print" class="flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl font-semibold bg-white border-2 border-slate-200 text-slate-600 hover:bg-slate-50 transition-all disabled:opacity-30" disabled><i data-lucide="printer" class="w-4 h-4"></i> Print</button>
                </div>
                <form id="save-form" method="POST" action="<?= url('/generate?type=' . $selectedType) ?>" style="display:none">
                    <input type="hidden" name="save_qr" value="1">
                    <input type="hidden" name="qr_type" value="<?= $selectedType ?>">
                    <input type="hidden" name="qr_title" id="form-qr-title">
                    <input type="hidden" name="qr_content" id="form-qr-content">
                    <input type="hidden" name="fg_color" id="form-fg-color">
                    <input type="hidden" name="bg_color" id="form-bg-color">
                    <input type="hidden" name="qr_size" id="form-qr-size">
                    <input type="hidden" name="error_correction" id="form-qr-ec">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let qrInstance = null;
const currentType = '<?= $selectedType ?>';
function getQRContent() {
    switch (currentType) {
        case 'url': return document.getElementById('qr-url')?.value||'';
        case 'text': return document.getElementById('qr-text')?.value||'';
        case 'email': { const e=document.getElementById('qr-email')?.value||''; const s=document.getElementById('qr-email-subject')?.value||''; const b=document.getElementById('qr-email-body')?.value||''; return e?`mailto:${e}?subject=${encodeURIComponent(s)}&body=${encodeURIComponent(b)}`:''; }
        case 'phone': { const p=document.getElementById('qr-phone')?.value||''; return p?'tel:'+p:''; }
        case 'sms': { const p=document.getElementById('qr-sms-phone')?.value||''; const m=document.getElementById('qr-sms-message')?.value||''; return p?`sms:${p}?body=${encodeURIComponent(m)}`:''; }
        case 'whatsapp': { const p=(document.getElementById('qr-wa-phone')?.value||'').replace(/[^0-9]/g,''); const m=document.getElementById('qr-wa-message')?.value||''; return p?`https://wa.me/${p}${m?'?text='+encodeURIComponent(m):''}`:''; }
        case 'wifi': { const s=document.getElementById('qr-wifi-ssid')?.value||''; const p=document.getElementById('qr-wifi-password')?.value||''; const e=document.getElementById('qr-wifi-encryption')?.value||'WPA'; const h=document.getElementById('qr-wifi-hidden')?.checked?'true':'false'; return s?`WIFI:T:${e};S:${s};P:${p};H:${h};;`:''; }
        case 'vcard': { const fn=document.getElementById('qr-vc-fname')?.value||''; const ln=document.getElementById('qr-vc-lname')?.value||''; if(!fn&&!ln)return''; let v=`BEGIN:VCARD\nVERSION:3.0\nN:${ln};${fn}\nFN:${fn} ${ln}`; const o=document.getElementById('qr-vc-org')?.value;if(o)v+=`\nORG:${o}`; const t=document.getElementById('qr-vc-title')?.value;if(t)v+=`\nTITLE:${t}`; const p=document.getElementById('qr-vc-phone')?.value;if(p)v+=`\nTEL:${p}`; const e=document.getElementById('qr-vc-email')?.value;if(e)v+=`\nEMAIL:${e}`; const u=document.getElementById('qr-vc-url')?.value;if(u)v+=`\nURL:${u}`; const a=document.getElementById('qr-vc-address')?.value;if(a)v+=`\nADR:;;${a}`; v+='\nEND:VCARD'; return v; }
        case 'event': { const t=document.getElementById('qr-ev-title')?.value||''; if(!t)return''; const st=(document.getElementById('qr-ev-start')?.value||'').replace(/[-:T]/g,'')+'00'; const en=(document.getElementById('qr-ev-end')?.value||'').replace(/[-:T]/g,'')+'00'; const l=document.getElementById('qr-ev-location')?.value||''; const d=document.getElementById('qr-ev-desc')?.value||''; return`BEGIN:VCALENDAR\nVERSION:2.0\nBEGIN:VEVENT\nSUMMARY:${t}\nDTSTART:${st}\nDTEND:${en}\nLOCATION:${l}\nDESCRIPTION:${d}\nEND:VEVENT\nEND:VCALENDAR`; }
        case 'geo': { const la=document.getElementById('qr-geo-lat')?.value||''; const lo=document.getElementById('qr-geo-lng')?.value||''; const q=document.getElementById('qr-geo-query')?.value||''; return(la&&lo)?`geo:${la},${lo}${q?'?q='+encodeURIComponent(q):''}`:''; }
        case 'upi': { const pa=document.getElementById('qr-upi-pa')?.value||''; if(!pa)return''; const pn=document.getElementById('qr-upi-pn')?.value||''; const am=document.getElementById('qr-upi-am')?.value||''; const tn=document.getElementById('qr-upi-tn')?.value||''; let u=`upi://pay?pa=${pa}&pn=${encodeURIComponent(pn)}`; if(am)u+=`&am=${am}`; if(tn)u+=`&tn=${encodeURIComponent(tn)}`; return u; }
        case 'bitcoin': { const a=document.getElementById('qr-btc-addr')?.value||''; const am=document.getElementById('qr-btc-amount')?.value||''; return a?`bitcoin:${a}${am?'?amount='+am:''}`:''; }
        case 'youtube': return document.getElementById('qr-yt-url')?.value||'';
        case 'twitter': { const h=document.getElementById('qr-tw-handle')?.value||''; return h?`https://twitter.com/${h}`:''; }
        case 'instagram': { const h=document.getElementById('qr-ig-handle')?.value||''; return h?`https://instagram.com/${h}`:''; }
        case 'facebook': { const u=document.getElementById('qr-fb-url')?.value||''; return u.startsWith('http')?u:(u?`https://facebook.com/${u}`:''); }
        case 'linkedin': { const u=document.getElementById('qr-li-url')?.value||''; return u.startsWith('http')?u:(u?`https://linkedin.com/in/${u}`:''); }
        case 'spotify': return document.getElementById('qr-sp-url')?.value||'';
        case 'zoom': return document.getElementById('qr-zm-url')?.value||'';
        case 'pdf': return document.getElementById('qr-pdf-url')?.value||'';
        case 'image': return document.getElementById('qr-img-url')?.value||'';
        case 'mecard': { const fn=document.getElementById('qr-mc-fname')?.value||''; const ln=document.getElementById('qr-mc-lname')?.value||''; if(!fn&&!ln)return''; let m=`MECARD:N:${ln},${fn};`; const p=document.getElementById('qr-mc-phone')?.value;if(p)m+=`TEL:${p};`; const e=document.getElementById('qr-mc-email')?.value;if(e)m+=`EMAIL:${e};`; m+=';'; return m; }
        case 'appstore': return document.getElementById('qr-app-url')?.value||'';
        default: return '';
    }
}
function updateQR() {
    const content=getQRContent(); const container=document.getElementById('qr-code'); const placeholder=document.getElementById('qr-placeholder');
    const size=parseInt(document.getElementById('qr-size').value); const fg=document.getElementById('qr-fg-color').value; const bg=document.getElementById('qr-bg-color').value;
    const ecMap={L:1,M:0,Q:3,H:2}; const ec=ecMap[document.getElementById('qr-ec').value]||2;
    document.getElementById('qr-fg-hex').value=fg; document.getElementById('qr-bg-hex').value=bg;
    const btns=['btn-download-png','btn-download-svg','btn-save','btn-print'];
    if(!content){container.innerHTML='';container.style.display='none';placeholder.style.display='block';btns.forEach(b=>document.getElementById(b).disabled=true);return;}
    container.style.display='block';placeholder.style.display='none';container.innerHTML='';container.style.backgroundColor=bg;btns.forEach(b=>document.getElementById(b).disabled=false);
    qrInstance=new QRCode(container,{text:content,width:Math.min(size,300),height:Math.min(size,300),colorDark:fg,colorLight:bg,correctLevel:ec});
}
function downloadQR(fmt){const c=getQRContent();if(!c)return;const cv=document.querySelector('#qr-code canvas');if(!cv)return;if(fmt==='png'){const s=parseInt(document.getElementById('qr-size').value);const h=document.createElement('canvas');h.width=s;h.height=s;const x=h.getContext('2d');x.imageSmoothingEnabled=false;x.drawImage(cv,0,0,s,s);const l=document.createElement('a');l.download=`qrcode_${currentType}_${Date.now()}.png`;l.href=h.toDataURL('image/png');l.click();}else{const d=cv.toDataURL('image/png');const s=parseInt(document.getElementById('qr-size').value);const svg=`<svg xmlns="http://www.w3.org/2000/svg" width="${s}" height="${s}"><image width="${s}" height="${s}" href="${d}"/></svg>`;const b=new Blob([svg],{type:'image/svg+xml'});const l=document.createElement('a');l.download=`qrcode_${currentType}_${Date.now()}.svg`;l.href=URL.createObjectURL(b);l.click();}}
function saveQRToDB(){const c=getQRContent();if(!c)return;document.getElementById('form-qr-title').value=document.getElementById('qr-save-title').value||'<?= addslashes($types[$selectedType]['name']) ?>';document.getElementById('form-qr-content').value=c;document.getElementById('form-fg-color').value=document.getElementById('qr-fg-color').value;document.getElementById('form-bg-color').value=document.getElementById('qr-bg-color').value;document.getElementById('form-qr-size').value=document.getElementById('qr-size').value;document.getElementById('form-qr-ec').value=document.getElementById('qr-ec').value;document.getElementById('save-form').submit();}
function printQR(){const cv=document.querySelector('#qr-code canvas');if(!cv)return;const w=window.open('','_blank');w.document.write(`<html><head><title>QR Code</title></head><body style="display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;"><img src="${cv.toDataURL()}" style="max-width:400px;"></body></html>`);w.document.close();w.onload=()=>{w.print();};}
document.addEventListener('DOMContentLoaded',()=>{lucide.createIcons();});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
