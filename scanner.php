<?php
// ── Auth BEFORE any HTML output ───────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

// ── Now safe to output HTML ────────────────────────────────────────────────────
$pageTitle = 'QR Scanner';
require_once __DIR__ . '/includes/header.php';
?>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<style>
    #scanner-region video { border-radius: 16px !important; }
    #scanner-region img { display: none; }
    #scanner-region { border: none !important; }
    #html5-qrcode-button-camera-permission,
    #html5-qrcode-button-camera-start,
    #html5-qrcode-button-camera-stop,
    #html5-qrcode-anchor-scan-type-change,
    #html5-qrcode-button-file-selection { display: none !important; }
    #qr-shaded-region { border-color: var(--orange) !important; }
    .scanner-viewfinder { position: relative; overflow: hidden; border-radius: 20px; background: #0F172A; }
    .scanner-viewfinder::before {
        content: '';
        position: absolute; inset: 0; z-index: 5; pointer-events: none;
        border: 3px solid rgba(255,92,53,0.4);
        border-radius: 20px;
    }
    .scan-line {
        position: absolute; left: 10%; right: 10%; height: 2px; z-index: 10; pointer-events: none;
        background: linear-gradient(90deg, transparent, var(--orange), transparent);
        box-shadow: 0 0 15px rgba(255,92,53,0.5);
        animation: scanLine 2.5s ease-in-out infinite;
    }
    @keyframes scanLine { 0%,100%{top:15%} 50%{top:85%} }
    .pulse-ring {
        position: absolute; inset: -4px; border: 2px solid var(--orange); border-radius: 24px;
        animation: pulseRing 2s ease-out infinite; opacity: 0;
    }
    @keyframes pulseRing { 0%{transform:scale(1);opacity:0.5} 100%{transform:scale(1.05);opacity:0} }
    .result-reveal { animation: resultReveal 0.5s cubic-bezier(0.16,1,0.3,1) forwards; }
    @keyframes resultReveal { from{opacity:0;transform:translateY(20px) scale(0.97)} to{opacity:1;transform:translateY(0) scale(1)} }
    .success-check {
        width: 56px; height: 56px; border-radius: 50%;
        background: linear-gradient(135deg, #10B981, #059669);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 20px rgba(16,185,129,0.3);
        animation: checkBounce 0.5s cubic-bezier(0.34,1.56,0.64,1);
    }
    @keyframes checkBounce { 0%{transform:scale(0)} 60%{transform:scale(1.15)} 100%{transform:scale(1)} }
    .drop-zone { transition: all 0.3s; }
    .drop-zone.drag-over { border-color: var(--orange) !important; background: rgba(255,92,53,0.04) !important; transform: scale(1.01); }
</style>

<div class="max-w-4xl mx-auto px-4 sm:px-5 py-10">

    <!-- Header -->
    <div class="text-center mb-10 a-up">
        <p class="section-tag mb-2">Scanner</p>
        <h1 class="font-display font-extrabold text-3xl sm:text-4xl text-neutral-900">QR Code Scanner</h1>
        <p class="text-neutral-500 mt-2 max-w-lg mx-auto">Scan any QR code using your camera or upload an image to instantly decode its content</p>
    </div>

    <!-- Mode Tabs -->
    <div class="flex items-center justify-center gap-2 mb-8 a-up d1">
        <button id="tab-camera" onclick="switchTab('camera')" class="btn btn-p">
            <i data-lucide="camera" class="w-4 h-4"></i> Camera
        </button>
        <button id="tab-upload" onclick="switchTab('upload')" class="btn btn-s">
            <i data-lucide="upload" class="w-4 h-4"></i> Upload Image
        </button>
    </div>

    <!-- Camera Scanner -->
    <div id="panel-camera" class="a-up d2">
        <div class="card p-6 sm:p-8" style="border-radius:24px">

            <!-- Scanner viewport -->
            <div class="scanner-viewfinder mx-auto mb-6" style="max-width:480px; min-height:320px" id="scanner-container">
                <div class="scan-line" id="scan-line" style="display:none"></div>
                <div class="pulse-ring" id="pulse-ring" style="display:none"></div>
                <div id="scanner-region" style="width:100%"></div>

                <!-- Placeholder before camera starts -->
                <div id="scanner-placeholder" class="flex flex-col items-center justify-center text-center p-10" style="min-height:320px">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-4" style="background:var(--orange-light)">
                        <i data-lucide="scan-line" class="w-10 h-10" style="color:var(--orange)"></i>
                    </div>
                    <p class="text-neutral-400 text-sm font-medium">Press the button below to start scanning</p>
                </div>
            </div>

            <!-- Camera controls -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <button id="btn-start" onclick="startScanner()" class="btn btn-p btn-lg w-full sm:w-auto">
                    <i data-lucide="play" class="w-5 h-5"></i> Start Scanner
                </button>
                <button id="btn-stop" onclick="stopScanner()" class="btn btn-s btn-lg w-full sm:w-auto" style="display:none">
                    <i data-lucide="square" class="w-5 h-5"></i> Stop Scanner
                </button>
                <button id="btn-switch" onclick="switchCamera()" class="btn btn-s btn-lg" style="display:none" title="Switch Camera">
                    <i data-lucide="switch-camera" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Camera selector -->
            <div id="camera-select-wrapper" class="mt-4" style="display:none">
                <label class="label text-center">Select Camera</label>
                <select id="camera-select" class="field max-w-xs mx-auto block" onchange="onCameraChange()"></select>
            </div>
        </div>
    </div>

    <!-- Upload Scanner -->
    <div id="panel-upload" style="display:none" class="a-up d2">
        <div class="card p-6 sm:p-8" style="border-radius:24px">

            <div id="drop-zone" class="drop-zone border-2 border-dashed border-neutral-200 rounded-2xl p-12 text-center cursor-pointer transition-all hover:border-orange-300"
                 onclick="document.getElementById('file-input').click()">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:var(--orange-light)">
                    <i data-lucide="image-plus" class="w-8 h-8" style="color:var(--orange)"></i>
                </div>
                <h3 class="font-display font-bold text-lg text-neutral-700 mb-1">Drop an image here</h3>
                <p class="text-neutral-400 text-sm mb-4">or click to browse files</p>
                <span class="badge badge-gr">PNG, JPG, WEBP, GIF</span>
                <input type="file" id="file-input" accept="image/*" class="hidden" onchange="scanFromFile(this)">
            </div>

            <!-- Image preview -->
            <div id="upload-preview" class="mt-6 text-center" style="display:none">
                <img id="preview-img" class="max-h-64 mx-auto rounded-2xl border border-neutral-200 shadow-soft" alt="Uploaded image">
                <p id="upload-scanning" class="text-sm text-neutral-500 mt-3 font-medium">
                    <i data-lucide="loader" class="w-4 h-4 inline animate-spin"></i> Scanning image...
                </p>
            </div>
        </div>
    </div>

    <!-- Result Panel -->
    <div id="result-panel" class="mt-8" style="display:none">
        <div class="card p-6 sm:p-8 result-reveal" style="border-radius:24px; border-color: #10B981; border-width: 1.5px;">

            <!-- Success header -->
            <div class="flex flex-col items-center text-center mb-6">
                <div class="success-check mb-4">
                    <i data-lucide="check" class="w-7 h-7 text-white"></i>
                </div>
                <h3 class="font-display font-bold text-xl text-neutral-900">QR Code Decoded!</h3>
                <p class="text-neutral-400 text-sm mt-1">Content found successfully</p>
            </div>

            <!-- Result type badge -->
            <div class="flex justify-center mb-4">
                <span id="result-type-badge" class="badge badge-o"></span>
            </div>

            <!-- Decoded content -->
            <div class="rounded-2xl p-5 mb-6" style="background:var(--bg2); border: 1px solid var(--border)">
                <label class="label mb-2">Decoded Content</label>
                <div id="result-content" class="text-neutral-800 text-sm font-medium break-all leading-relaxed"></div>
                <div id="result-link" class="mt-3" style="display:none">
                    <a id="result-url" href="#" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 text-sm font-semibold hover:underline" style="color:var(--orange)">
                        <i data-lucide="external-link" class="w-4 h-4"></i> Open Link
                    </a>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <button onclick="copyResult()" id="btn-copy" class="btn btn-p w-full sm:w-auto">
                    <i data-lucide="copy" class="w-4 h-4"></i> <span id="copy-label">Copy to Clipboard</span>
                </button>
                <button onclick="scanAgain()" class="btn btn-s w-full sm:w-auto">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i> Scan Again
                </button>
                <a id="btn-generate" href="#" class="btn btn-s w-full sm:w-auto" style="display:none">
                    <i data-lucide="qr-code" class="w-4 h-4"></i> Generate QR
                </a>
            </div>
        </div>
    </div>

    <!-- Scan history (session only) -->
    <div id="scan-history" class="mt-8" style="display:none">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display font-bold text-lg text-neutral-900">
                <i data-lucide="list" class="w-5 h-5 inline mr-1" style="color:var(--orange)"></i> Recent Scans
            </h2>
            <button onclick="clearHistory()" class="btn btn-s btn-sm">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Clear
            </button>
        </div>
        <div id="history-list" class="space-y-2"></div>
    </div>

    <!-- Tips -->
    <div class="mt-10 a-up d3">
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="card p-5 text-center" style="border-radius:16px">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-3 bg-blue-50">
                    <i data-lucide="focus" class="w-5 h-5 text-blue-500"></i>
                </div>
                <h4 class="font-display font-bold text-sm text-neutral-800 mb-1">Hold Steady</h4>
                <p class="text-xs text-neutral-400">Keep the QR code centered and in focus for best results</p>
            </div>
            <div class="card p-5 text-center" style="border-radius:16px">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-3 bg-amber-50">
                    <i data-lucide="sun" class="w-5 h-5 text-amber-500"></i>
                </div>
                <h4 class="font-display font-bold text-sm text-neutral-800 mb-1">Good Lighting</h4>
                <p class="text-xs text-neutral-400">Ensure the QR code is well-lit and free from glare</p>
            </div>
            <div class="card p-5 text-center" style="border-radius:16px">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-3 bg-green-50">
                    <i data-lucide="image" class="w-5 h-5 text-green-500"></i>
                </div>
                <h4 class="font-display font-bold text-sm text-neutral-800 mb-1">Upload Works Too</h4>
                <p class="text-xs text-neutral-400">Got a screenshot? Switch to upload mode to scan it</p>
            </div>
        </div>
    </div>

</div>

<script>
    let html5QrCode = null;
    let scanning = false;
    let cameras = [];
    let currentCameraIdx = 0;
    let scanResults = [];

    // ── Tab switching ────────────────────────────────────────────────────────────
    function switchTab(tab) {
        const camBtn   = document.getElementById('tab-camera');
        const upBtn    = document.getElementById('tab-upload');
        const camPanel = document.getElementById('panel-camera');
        const upPanel  = document.getElementById('panel-upload');

        if (tab === 'camera') {
            camBtn.className  = 'btn btn-p';
            upBtn.className   = 'btn btn-s';
            camPanel.style.display = '';
            upPanel.style.display  = 'none';
        } else {
            stopScanner();
            upBtn.className   = 'btn btn-p';
            camBtn.className  = 'btn btn-s';
            upPanel.style.display  = '';
            camPanel.style.display = 'none';
        }
    }

    // ── Camera scanner ───────────────────────────────────────────────────────────
    function startScanner() {
        if (scanning) return;

        document.getElementById('scanner-placeholder').style.display = 'none';
        document.getElementById('btn-start').style.display  = 'none';
        document.getElementById('btn-stop').style.display   = '';
        document.getElementById('scan-line').style.display  = '';
        document.getElementById('pulse-ring').style.display = '';
        document.getElementById('result-panel').style.display = 'none';

        html5QrCode = new Html5Qrcode("scanner-region");

        Html5Qrcode.getCameras().then(deviceList => {
            cameras = deviceList;
            if (cameras.length === 0) {
                showError('No cameras found on this device.');
                return;
            }

            // Populate camera selector
            if (cameras.length > 1) {
                const select = document.getElementById('camera-select');
                select.innerHTML = '';
                cameras.forEach((cam, i) => {
                    const opt = document.createElement('option');
                    opt.value = i;
                    opt.text = cam.label || ('Camera ' + (i + 1));
                    select.appendChild(opt);
                });
                document.getElementById('camera-select-wrapper').style.display = '';
                document.getElementById('btn-switch').style.display = '';
            }

            // Prefer back camera
            currentCameraIdx = cameras.length > 1 ? 1 : 0;
            startCameraById(cameras[currentCameraIdx].id);
        }).catch(err => {
            showError('Camera access denied. Please allow camera permissions.');
            resetScannerUI();
        });
    }

    function startCameraById(cameraId) {
        scanning = true;
        html5QrCode.start(
            cameraId,
            { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
            onScanSuccess,
            () => {} // ignore scan failures (no QR in frame)
        ).catch(err => {
            showError('Could not start camera: ' + err.message);
            resetScannerUI();
        });
    }

    function stopScanner() {
        if (html5QrCode && scanning) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                scanning = false;
                resetScannerUI();
            }).catch(() => {
                scanning = false;
                resetScannerUI();
            });
        }
    }

    function switchCamera() {
        if (!scanning || cameras.length < 2) return;
        currentCameraIdx = (currentCameraIdx + 1) % cameras.length;
        document.getElementById('camera-select').value = currentCameraIdx;
        html5QrCode.stop().then(() => {
            startCameraById(cameras[currentCameraIdx].id);
        });
    }

    function onCameraChange() {
        const idx = parseInt(document.getElementById('camera-select').value);
        if (idx !== currentCameraIdx && scanning) {
            currentCameraIdx = idx;
            html5QrCode.stop().then(() => {
                startCameraById(cameras[currentCameraIdx].id);
            });
        }
    }

    function resetScannerUI() {
        document.getElementById('btn-start').style.display  = '';
        document.getElementById('btn-stop').style.display   = 'none';
        document.getElementById('btn-switch').style.display = 'none';
        document.getElementById('scan-line').style.display  = 'none';
        document.getElementById('pulse-ring').style.display = 'none';
        document.getElementById('camera-select-wrapper').style.display = 'none';
        document.getElementById('scanner-placeholder').style.display = '';
    }

    // ── File upload scanner ──────────────────────────────────────────────────────
    function scanFromFile(input) {
        if (!input.files || !input.files.length) return;

        const file = input.files[0];
        const preview = document.getElementById('upload-preview');
        const previewImg = document.getElementById('preview-img');

        // Show preview
        const reader = new FileReader();
        reader.onload = e => {
            previewImg.src = e.target.result;
            preview.style.display = '';
            document.getElementById('upload-scanning').style.display = '';
        };
        reader.readAsDataURL(file);

        // Scan
        const tempScanner = new Html5Qrcode("upload-temp-scanner");
        html5QrCode = tempScanner;

        tempScanner.scanFile(file, true).then(decoded => {
            document.getElementById('upload-scanning').style.display = 'none';
            onScanSuccess(decoded);
            tempScanner.clear();
        }).catch(() => {
            document.getElementById('upload-scanning').innerHTML =
                '<i data-lucide="alert-circle" class="w-4 h-4 inline text-red-400"></i> <span class="text-red-500">No QR code found in this image. Try another image.</span>';
            lucide.createIcons();
        });

        // Reset input so same file can be re-selected
        input.value = '';
    }

    // ── Drag & drop ──────────────────────────────────────────────────────────────
    (function() {
        const dropZone = document.getElementById('drop-zone');
        if (!dropZone) return;

        ['dragenter', 'dragover'].forEach(ev => {
            dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
        });
        ['dragleave', 'drop'].forEach(ev => {
            dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.remove('drag-over'); });
        });
        dropZone.addEventListener('drop', e => {
            const files = e.dataTransfer.files;
            if (files.length) {
                const input = document.getElementById('file-input');
                input.files = files;
                scanFromFile(input);
            }
        });
    })();

    // ── On successful scan ───────────────────────────────────────────────────────
    function onScanSuccess(decoded) {
        // Stop camera if running
        if (scanning && html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                scanning = false;
                resetScannerUI();
            }).catch(() => {});
        }

        const text  = decoded;
        const isUrl = /^https?:\/\//i.test(text);
        const isEmail = /^mailto:/i.test(text) || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(text);
        const isPhone = /^tel:/i.test(text);
        const isWifi = /^WIFI:/i.test(text);

        // Determine type
        let typeName = 'Text';
        let typeIcon = 'type';
        if (isUrl)        { typeName = 'URL';   typeIcon = 'link'; }
        else if (isEmail) { typeName = 'Email'; typeIcon = 'mail'; }
        else if (isPhone) { typeName = 'Phone'; typeIcon = 'phone'; }
        else if (isWifi)  { typeName = 'WiFi';  typeIcon = 'wifi'; }

        // Show result
        const panel = document.getElementById('result-panel');
        panel.style.display = '';
        panel.querySelector('.card').classList.remove('result-reveal');
        void panel.querySelector('.card').offsetWidth; // trigger reflow
        panel.querySelector('.card').classList.add('result-reveal');

        document.getElementById('result-type-badge').innerHTML =
            '<i data-lucide="' + typeIcon + '" class="w-3 h-3 mr-1"></i> ' + typeName;

        document.getElementById('result-content').textContent = text;

        // URL link
        const linkEl = document.getElementById('result-link');
        const urlEl  = document.getElementById('result-url');
        if (isUrl) {
            linkEl.style.display = '';
            urlEl.href = text;
        } else {
            linkEl.style.display = 'none';
        }

        // Generate QR button
        const genBtn = document.getElementById('btn-generate');
        if (isUrl) {
            genBtn.style.display = '';
            genBtn.href = '<?= url("/generate?type=url") ?>&content=' + encodeURIComponent(text);
        } else {
            genBtn.style.display = '';
            genBtn.href = '<?= url("/generate?type=text") ?>&content=' + encodeURIComponent(text);
        }

        // Reset copy button
        document.getElementById('copy-label').textContent = 'Copy to Clipboard';

        // Add to session history
        addToHistory(text, typeName);

        // Scroll to result
        panel.scrollIntoView({ behavior: 'smooth', block: 'center' });

        lucide.createIcons();
    }

    // ── Copy to clipboard ────────────────────────────────────────────────────────
    function copyResult() {
        const text = document.getElementById('result-content').textContent;
        navigator.clipboard.writeText(text).then(() => {
            const label = document.getElementById('copy-label');
            label.textContent = 'Copied!';
            setTimeout(() => { label.textContent = 'Copy to Clipboard'; }, 2000);
        });
    }

    // ── Scan again ───────────────────────────────────────────────────────────────
    function scanAgain() {
        document.getElementById('result-panel').style.display = 'none';

        // Reset upload preview
        document.getElementById('upload-preview').style.display = 'none';
        document.getElementById('upload-scanning').style.display = '';
        document.getElementById('upload-scanning').innerHTML =
            '<i data-lucide="loader" class="w-4 h-4 inline animate-spin"></i> Scanning image...';

        // If on camera tab, restart
        if (document.getElementById('panel-camera').style.display !== 'none') {
            startScanner();
        }
    }

    // ── Session scan history ─────────────────────────────────────────────────────
    function addToHistory(text, type) {
        scanResults.unshift({ text, type, time: new Date() });
        if (scanResults.length > 20) scanResults.pop();
        renderHistory();
    }

    function renderHistory() {
        const container = document.getElementById('scan-history');
        const list = document.getElementById('history-list');

        if (scanResults.length === 0) {
            container.style.display = 'none';
            return;
        }
        container.style.display = '';

        list.innerHTML = scanResults.map((item, i) => {
            const isUrl = /^https?:\/\//i.test(item.text);
            const timeStr = item.time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            return `
                <div class="card p-4 flex items-center gap-4" style="border-radius:14px">
                    <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center" style="background:var(--orange-light)">
                        <i data-lucide="qr-code" class="w-4 h-4" style="color:var(--orange)"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-neutral-800 truncate">${escapeHtml(item.text)}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="badge badge-gr">${item.type}</span>
                            <span class="text-xs text-neutral-400">${timeStr}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        ${isUrl ? `<a href="${escapeHtml(item.text)}" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg flex items-center justify-center text-neutral-400 hover:text-blue-500 hover:bg-blue-50 transition-all" title="Open"><i data-lucide="external-link" class="w-4 h-4"></i></a>` : ''}
                        <button onclick="navigator.clipboard.writeText('${escapeJs(item.text)}')" class="w-8 h-8 rounded-lg flex items-center justify-center text-neutral-400 hover:text-green-500 hover:bg-green-50 transition-all" title="Copy">
                            <i data-lucide="copy" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>`;
        }).join('');

        lucide.createIcons();
    }

    function clearHistory() {
        scanResults = [];
        renderHistory();
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function escapeJs(str) {
        return str.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    // ── Error helper ─────────────────────────────────────────────────────────────
    function showError(msg) {
        const panel = document.getElementById('result-panel');
        panel.style.display = '';
        panel.innerHTML = `
            <div class="alert alert-err" style="border-radius:16px">
                <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                <span>${msg}</span>
            </div>`;
        lucide.createIcons();
    }

    // ── Init icons ───────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => { lucide.createIcons(); });
</script>

<!-- Hidden container for file-based scanning -->
<div id="upload-temp-scanner" style="display:none"></div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
