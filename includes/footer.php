</div><!-- end pt-16 -->

<footer class="mt-20 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center">
                        <i data-lucide="qr-code" class="w-5 h-5 text-white"></i>
                    </div>
                    <span class="font-display font-bold text-xl text-slate-800">QR<span class="text-brand-500">Code</span> Pro</span>
                </div>
                <p class="text-slate-500 text-sm max-w-sm leading-relaxed">Professional QR code generator with 24+ code types, bulk generation, PDF export, and full customization. Free &amp; open source.</p>
            </div>
            <div>
                <h4 class="font-display font-semibold text-xs uppercase tracking-wider text-slate-400 mb-4">Generators</h4>
                <div class="space-y-2.5">
                    <a href="<?= url('/generate?type=url') ?>" class="block text-sm text-slate-500 hover:text-brand-600 transition-colors">URL QR Code</a>
                    <a href="<?= url('/generate?type=wifi') ?>" class="block text-sm text-slate-500 hover:text-brand-600 transition-colors">WiFi QR Code</a>
                    <a href="<?= url('/generate?type=vcard') ?>" class="block text-sm text-slate-500 hover:text-brand-600 transition-colors">vCard QR Code</a>
                    <a href="<?= url('/bulk') ?>" class="block text-sm text-slate-500 hover:text-brand-600 transition-colors">Bulk Generator</a>
                    <a href="<?= url('/scanner') ?>" class="block text-sm text-slate-500 hover:text-brand-600 transition-colors">QR Scanner</a>
                </div>
            </div>
            <div>
                <h4 class="font-display font-semibold text-xs uppercase tracking-wider text-slate-400 mb-4">Account</h4>
                <div class="space-y-2.5">
                    <a href="<?= url('/login') ?>" class="block text-sm text-slate-500 hover:text-brand-600 transition-colors">Login</a>
                    <a href="<?= url('/register') ?>" class="block text-sm text-slate-500 hover:text-brand-600 transition-colors">Register</a>
                    <a href="<?= url('/dashboard') ?>" class="block text-sm text-slate-500 hover:text-brand-600 transition-colors">Dashboard</a>
                </div>
            </div>
            <div>
                <h4 class="font-display font-semibold text-xs uppercase tracking-wider text-slate-400 mb-4">Company</h4>
                <div class="space-y-2.5">
                    <a href="<?= url('/about') ?>" class="block text-sm text-slate-500 hover:text-brand-600 transition-colors">About Us</a>
                    <a href="<?= url('/contact') ?>" class="block text-sm text-slate-500 hover:text-brand-600 transition-colors">Contact</a>
                    <a href="<?= url('/privacy') ?>" class="block text-sm text-slate-500 hover:text-brand-600 transition-colors">Privacy Policy</a>
                </div>
            </div>
        </div>
        <div class="mt-10 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-slate-400 text-xs">&copy; <?= date('Y') ?> QRCode Pro. Open Source &amp; Free Forever.</p>
            <p class="text-slate-400 text-xs">Crafted with PHP, Tailwind CSS &amp; ♥</p>
        </div>
    </div>
</footer>

<script>lucide.createIcons();</script>
</body>
</html>
