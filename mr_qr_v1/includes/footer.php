</div><!-- /pt-16 -->

<footer class="border-t border-neutral-200 bg-neutral-50 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div class="sm:col-span-2 lg:col-span-2">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:var(--orange)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/></svg>
                    </div>
                    <span style="font-family:'Syne',sans-serif;font-weight:800;font-size:18px;color:#111110">QR<span style="color:var(--orange)">Pro</span></span>
                </div>
                <p class="text-sm text-neutral-500 max-w-xs leading-relaxed">Professional QR code generator with 24+ types, bulk generation, PDF export, and full customization. Free &amp; open source.</p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-4">Generators</p>
                <div class="space-y-2.5">
                    <a href="<?= url('/generate?type=url') ?>" class="block text-sm text-neutral-500 hover:text-neutral-800 transition-colors font-medium">URL QR Code</a>
                    <a href="<?= url('/generate?type=wifi') ?>" class="block text-sm text-neutral-500 hover:text-neutral-800 transition-colors font-medium">WiFi QR Code</a>
                    <a href="<?= url('/generate?type=vcard') ?>" class="block text-sm text-neutral-500 hover:text-neutral-800 transition-colors font-medium">vCard QR Code</a>
                    <a href="<?= url('/bulk') ?>" class="block text-sm text-neutral-500 hover:text-neutral-800 transition-colors font-medium">Bulk Generator</a>
                </div>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-4">Account</p>
                <div class="space-y-2.5">
                    <a href="<?= url('/login') ?>" class="block text-sm text-neutral-500 hover:text-neutral-800 transition-colors font-medium">Login</a>
                    <a href="<?= url('/register') ?>" class="block text-sm text-neutral-500 hover:text-neutral-800 transition-colors font-medium">Register</a>
                    <a href="<?= url('/dashboard') ?>" class="block text-sm text-neutral-500 hover:text-neutral-800 transition-colors font-medium">Dashboard</a>
                    <a href="<?= url('/history') ?>" class="block text-sm text-neutral-500 hover:text-neutral-800 transition-colors font-medium">My QR Codes</a>
                </div>
            </div>
        </div>
        <div class="mt-12 pt-6 border-t border-neutral-200 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-neutral-400 text-xs font-medium">&copy; <?= date('Y') ?> QRCode Pro. Open Source &amp; Free Forever.</p>
            <p class="text-neutral-400 text-xs font-medium">Built with PHP + Tailwind + ♥</p>
        </div>
    </div>
</footer>

<script>
if(typeof lucide!=='undefined') lucide.createIcons();
</script>
</body>
</html>