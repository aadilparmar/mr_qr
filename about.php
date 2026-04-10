<?php
// ── No auth required — public page ───────────────────────────────────────────
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'About Us';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<section class="relative overflow-hidden hero-gradient">
    <div class="absolute top-24 left-10 w-20 h-20 rounded-2xl bg-brand-200/30 rotate-12 animate-float" style="animation-delay:0s"></div>
    <div class="absolute top-40 right-20 w-14 h-14 rounded-full bg-lilac-200/30 animate-float" style="animation-delay:1s"></div>
    <div class="absolute bottom-24 left-1/4 w-16 h-16 rounded-xl bg-sky-200/30 -rotate-12 animate-float" style="animation-delay:2s"></div>
    <div class="absolute top-52 right-1/3 w-10 h-10 rounded-lg bg-peach-200/30 rotate-45 animate-float" style="animation-delay:0.5s"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-20 md:py-28 relative z-10 text-center">
        <div class="inline-flex items-center gap-2 bg-white/70 backdrop-blur-sm rounded-full px-5 py-2 mb-8 border border-brand-100 shadow-soft animate-fade-in-up">
            <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
            <span class="text-xs font-semibold text-brand-600 tracking-wide uppercase">Open Source &bull; Free Forever</span>
        </div>
        <h1 class="font-display text-4xl md:text-6xl font-extrabold tracking-tight mb-6 text-slate-900 animate-fade-in-up delay-100">
            Built by <span class="gradient-text">Students</span>, for Everyone
        </h1>
        <p class="text-lg md:text-xl text-slate-500 max-w-2xl mx-auto leading-relaxed animate-fade-in-up delay-200">
            QRCode Pro is a mini project developed by Computer Engineering students at Marwadi University, designed to make professional QR code generation accessible to all.
        </p>
    </div>
</section>

<!-- Mission -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">

            <div class="relative bg-gradient-to-br from-brand-500 via-brand-600 to-brand-700 rounded-3xl p-10 sm:p-12 overflow-hidden shadow-elevated animate-fade-in-up">
                <div class="absolute top-0 right-0 w-40 h-40 rounded-full bg-white/5 -translate-y-1/2 translate-x-1/4"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 rounded-full bg-white/5 translate-y-1/2 -translate-x-1/4"></div>
                <div class="relative z-10">
                    <p class="text-brand-200 text-sm font-semibold uppercase tracking-wider mb-4">Our Mission</p>
                    <h2 class="font-display text-3xl font-bold text-white mb-6">Making QR Codes Simple, Powerful & Free</h2>
                    <p class="text-brand-100 leading-relaxed mb-4">
                        We believe professional tools shouldn't cost a fortune. QRCode Pro provides enterprise-grade QR code generation with full customization, bulk processing, and 24+ QR types &mdash; completely free and open source.
                    </p>
                    <p class="text-brand-100 leading-relaxed">
                        From small businesses creating branded QR codes to developers integrating QR generation into their workflow, our tool is designed to be powerful yet effortlessly simple.
                    </p>
                </div>
            </div>

            <div class="space-y-6 animate-fade-in-up delay-200">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="zap" class="w-6 h-6 text-brand-500"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-slate-800 mb-1">24+ QR Types</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">URL, WiFi, vCard, UPI, WhatsApp, social media, calendar events, cryptocurrency, and many more &mdash; all from one unified interface.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-lilac-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="palette" class="w-6 h-6 text-lilac-500"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-slate-800 mb-1">Full Customization</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">Custom colors, dot styles, eye shapes, logos, frames, shadows, and error correction levels to match any brand.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-mint-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="layers" class="w-6 h-6 text-mint-500"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-slate-800 mb-1">Bulk Generation</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">Generate up to 100 QR codes at once from manual input or CSV upload, and download them all in a single ZIP archive.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="shield-check" class="w-6 h-6 text-sky-500"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-slate-800 mb-1">Privacy First</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">Self-hosted, open source, and your data stays on the server. No tracking, no ads, no third-party data sharing.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Team -->
<section class="py-20 bg-gradient-to-b from-brand-50/50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <p class="text-sm font-semibold text-brand-500 uppercase tracking-wider mb-3">Our Team</p>
            <h2 class="font-display text-3xl md:text-5xl font-bold text-slate-900 mb-4">Meet the Developers</h2>
            <p class="text-slate-500 max-w-xl mx-auto text-lg">Computer Engineering students passionate about building tools that make a difference.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <!-- Aadil -->
            <div class="bg-white rounded-3xl p-8 shadow-card border border-slate-100 card-hover text-center animate-fade-in-up">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-brand-400 to-lilac-400 flex items-center justify-center mx-auto mb-5 shadow-lg">
                    <span class="text-3xl font-extrabold text-white font-display">AP</span>
                </div>
                <h3 class="font-display text-xl font-bold text-slate-800 mb-1">Aadil Parmar</h3>
                <p class="text-brand-500 text-sm font-semibold mb-1">Lead Developer</p>
                <p class="text-slate-400 text-xs font-medium mb-4">92200103068</p>
                <div class="flex items-center justify-center gap-1 text-slate-400">
                    <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    <span class="text-xs font-medium">B.Tech CE, Marwadi University</span>
                </div>
            </div>

            <!-- Dhruvil -->
            <div class="bg-white rounded-3xl p-8 shadow-card border border-slate-100 card-hover text-center animate-fade-in-up delay-100">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-sky-400 to-brand-400 flex items-center justify-center mx-auto mb-5 shadow-lg">
                    <span class="text-3xl font-extrabold text-white font-display">DJ</span>
                </div>
                <h3 class="font-display text-xl font-bold text-slate-800 mb-1">Dhruvil Janani</h3>
                <p class="text-sky-500 text-sm font-semibold mb-1">Developer</p>
                <p class="text-slate-400 text-xs font-medium mb-4">92420103002</p>
                <div class="flex items-center justify-center gap-1 text-slate-400">
                    <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    <span class="text-xs font-medium">B.Tech CE, Marwadi University</span>
                </div>
            </div>

            <!-- Dhaval -->
            <div class="bg-white rounded-3xl p-8 shadow-card border border-slate-100 card-hover text-center animate-fade-in-up delay-200">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-mint-400 to-sky-400 flex items-center justify-center mx-auto mb-5 shadow-lg">
                    <span class="text-3xl font-extrabold text-white font-display">DC</span>
                </div>
                <h3 class="font-display text-xl font-bold text-slate-800 mb-1">Dhaval Chauhan</h3>
                <p class="text-mint-500 text-sm font-semibold mb-1">Developer</p>
                <p class="text-slate-400 text-xs font-medium mb-4">92100103297</p>
                <div class="flex items-center justify-center gap-1 text-slate-400">
                    <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    <span class="text-xs font-medium">B.Tech CE, Marwadi University</span>
                </div>
            </div>
        </div>

        <!-- Guide -->
        <div class="max-w-md mx-auto mt-10">
            <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 text-center animate-fade-in-up delay-300">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Project Guide</p>
                <h3 class="font-display text-lg font-bold text-slate-800">Prof. Niraj Bhagchandani</h3>
                <p class="text-sm text-slate-500 mt-1">Department of Computer Engineering</p>
                <p class="text-sm text-slate-500">Faculty of Engineering &amp; Technology</p>
            </div>
        </div>
    </div>
</section>

<!-- Tech Stack -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <p class="text-sm font-semibold text-brand-500 uppercase tracking-wider mb-3">Powered By</p>
            <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900">Technology Stack</h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 text-center card-hover animate-fade-in-up">
                <div class="w-16 h-16 rounded-2xl bg-brand-50 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="server" class="w-8 h-8 text-brand-500"></i>
                </div>
                <h3 class="font-display font-bold text-slate-800 mb-1">PHP 8.0+</h3>
                <p class="text-xs text-slate-400">Backend &amp; API</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 text-center card-hover animate-fade-in-up delay-100">
                <div class="w-16 h-16 rounded-2xl bg-lilac-50 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="database" class="w-8 h-8 text-lilac-500"></i>
                </div>
                <h3 class="font-display font-bold text-slate-800 mb-1">SQLite</h3>
                <p class="text-xs text-slate-400">Database</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 text-center card-hover animate-fade-in-up delay-200">
                <div class="w-16 h-16 rounded-2xl bg-sky-50 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="wind" class="w-8 h-8 text-sky-500"></i>
                </div>
                <h3 class="font-display font-bold text-slate-800 mb-1">Tailwind CSS</h3>
                <p class="text-xs text-slate-400">Styling</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 text-center card-hover animate-fade-in-up delay-300">
                <div class="w-16 h-16 rounded-2xl bg-mint-50 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="code-2" class="w-8 h-8 text-mint-500"></i>
                </div>
                <h3 class="font-display font-bold text-slate-800 mb-1">JavaScript</h3>
                <p class="text-xs text-slate-400">QR Engine</p>
            </div>
        </div>
    </div>
</section>

<!-- Project Info -->
<section class="py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-3xl p-8 md:p-10 shadow-card border border-slate-100 animate-fade-in-up">
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="font-display font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5 text-brand-500"></i> Project Details
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500 font-medium">Project</span>
                            <span class="text-sm font-semibold text-slate-800">QRCode Pro</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500 font-medium">Course</span>
                            <span class="text-sm font-semibold text-slate-800">Mini Project (01CE0609)</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500 font-medium">Team ID</span>
                            <span class="text-sm font-semibold text-slate-800">TC_084</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <span class="text-sm text-slate-500 font-medium">Semester</span>
                            <span class="text-sm font-semibold text-slate-800">6th Semester</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-slate-500 font-medium">License</span>
                            <span class="text-sm font-semibold text-slate-800">MIT (Open Source)</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="font-display font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                        <i data-lucide="link" class="w-5 h-5 text-brand-500"></i> Links
                    </h3>
                    <div class="space-y-3">
                        <a href="https://github.com/aadilparmar/mr_qr" target="_blank" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-slate-900 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="github" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 group-hover:text-brand-600 transition-colors">GitHub Repository</p>
                                <p class="text-xs text-slate-400">Source code &amp; documentation</p>
                            </div>
                            <i data-lucide="external-link" class="w-4 h-4 text-slate-300 ml-auto"></i>
                        </a>
                        <a href="<?= url('/contact') ?>" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-brand-500 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="mail" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 group-hover:text-brand-600 transition-colors">Contact Us</p>
                                <p class="text-xs text-slate-400">Questions &amp; feedback</p>
                            </div>
                            <i data-lucide="arrow-right" class="w-4 h-4 text-slate-300 ml-auto"></i>
                        </a>
                        <a href="<?= url('/privacy') ?>" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-lilac-500 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="shield" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 group-hover:text-brand-600 transition-colors">Privacy Policy</p>
                                <p class="text-xs text-slate-400">How we handle your data</p>
                            </div>
                            <i data-lucide="arrow-right" class="w-4 h-4 text-slate-300 ml-auto"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <div class="relative bg-gradient-to-br from-brand-500 via-brand-600 to-brand-700 rounded-3xl p-12 sm:p-16 overflow-hidden shadow-elevated">
            <div class="absolute top-0 right-0 w-40 h-40 rounded-full bg-white/5 -translate-y-1/2 translate-x-1/4"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 rounded-full bg-white/5 translate-y-1/2 -translate-x-1/4"></div>
            <div class="relative z-10">
                <h2 class="font-display text-3xl md:text-4xl font-bold mb-4 text-white">Ready to Create QR Codes?</h2>
                <p class="text-brand-200 mb-8 max-w-lg mx-auto text-lg">Start generating professional QR codes in seconds. No credit card, no limits.</p>
                <a href="<?= $currentUser ? url('/generate') : url('/register') ?>" class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl font-display font-bold text-brand-600 bg-white hover:bg-brand-50 hover:scale-105 transition-all shadow-lg text-base">
                    Get Started Free <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
