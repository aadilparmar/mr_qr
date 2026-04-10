<?php
// ── No auth required — public page ───────────────────────────────────────────
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Privacy Policy';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<section class="relative overflow-hidden hero-gradient">
    <div class="absolute top-20 right-16 w-14 h-14 rounded-2xl bg-lilac-200/30 rotate-12 animate-float" style="animation-delay:0.5s"></div>
    <div class="absolute bottom-16 left-20 w-12 h-12 rounded-full bg-brand-200/30 animate-float" style="animation-delay:1.5s"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16 md:py-20 relative z-10 text-center">
        <div class="inline-flex items-center gap-2 bg-white/70 backdrop-blur-sm rounded-full px-5 py-2 mb-6 border border-brand-100 shadow-soft animate-fade-in-up">
            <i data-lucide="shield" class="w-4 h-4 text-brand-500"></i>
            <span class="text-xs font-semibold text-brand-600 tracking-wide uppercase">Your Privacy Matters</span>
        </div>
        <h1 class="font-display text-4xl md:text-5xl font-extrabold text-slate-900 mb-4 animate-fade-in-up delay-100">Privacy Policy</h1>
        <p class="text-slate-500 max-w-xl mx-auto animate-fade-in-up delay-200">Last updated: April 2026</p>
    </div>
</section>

<!-- Content -->
<section class="py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-card border border-slate-100 animate-fade-in-up">

            <!-- Quick Nav -->
            <div class="rounded-2xl bg-slate-50 p-6 mb-10 border border-slate-100">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Contents</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <a href="#information" class="text-sm text-brand-500 hover:text-brand-700 font-medium transition-colors flex items-center gap-2">
                        <i data-lucide="chevron-right" class="w-3 h-3"></i> Information We Collect</a>
                    <a href="#usage" class="text-sm text-brand-500 hover:text-brand-700 font-medium transition-colors flex items-center gap-2">
                        <i data-lucide="chevron-right" class="w-3 h-3"></i> How We Use Your Information</a>
                    <a href="#storage" class="text-sm text-brand-500 hover:text-brand-700 font-medium transition-colors flex items-center gap-2">
                        <i data-lucide="chevron-right" class="w-3 h-3"></i> Data Storage &amp; Security</a>
                    <a href="#thirdparty" class="text-sm text-brand-500 hover:text-brand-700 font-medium transition-colors flex items-center gap-2">
                        <i data-lucide="chevron-right" class="w-3 h-3"></i> Third-Party Services</a>
                    <a href="#cookies" class="text-sm text-brand-500 hover:text-brand-700 font-medium transition-colors flex items-center gap-2">
                        <i data-lucide="chevron-right" class="w-3 h-3"></i> Cookies &amp; Sessions</a>
                    <a href="#rights" class="text-sm text-brand-500 hover:text-brand-700 font-medium transition-colors flex items-center gap-2">
                        <i data-lucide="chevron-right" class="w-3 h-3"></i> Your Rights</a>
                    <a href="#children" class="text-sm text-brand-500 hover:text-brand-700 font-medium transition-colors flex items-center gap-2">
                        <i data-lucide="chevron-right" class="w-3 h-3"></i> Children's Privacy</a>
                    <a href="#changes" class="text-sm text-brand-500 hover:text-brand-700 font-medium transition-colors flex items-center gap-2">
                        <i data-lucide="chevron-right" class="w-3 h-3"></i> Changes to This Policy</a>
                </div>
            </div>

            <!-- Intro -->
            <p class="text-slate-500 text-sm leading-relaxed mb-8">
                QRCode Pro ("we", "our", "us") is committed to protecting the privacy of our users. This Privacy Policy explains how we collect, use, store, and protect your information when you use our QR code generation service. By using QRCode Pro, you agree to the practices described in this policy.
            </p>

            <!-- Section 1 -->
            <div id="information" class="mb-10">
                <h2 class="font-display text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0 text-sm font-bold text-brand-500">1</span>
                    Information We Collect
                </h2>
                <p class="text-slate-500 text-sm leading-relaxed mb-4">We collect the following types of information:</p>
                <div class="space-y-3 ml-2">
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-brand-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Account Information:</span> Username, email address, full name, and a securely hashed password when you create an account.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-brand-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">QR Code Data:</span> The content you enter to generate QR codes (URLs, text, contact details, etc.), along with your customization settings and titles.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-brand-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Contact Messages:</span> Name, email, subject, and message content when you use our contact form.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-brand-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Usage Data:</span> Basic scan counts for QR codes and session data for authentication purposes.</p>
                    </div>
                </div>
            </div>

            <!-- Section 2 -->
            <div id="usage" class="mb-10">
                <h2 class="font-display text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0 text-sm font-bold text-brand-500">2</span>
                    How We Use Your Information
                </h2>
                <p class="text-slate-500 text-sm leading-relaxed mb-4">Your information is used to:</p>
                <div class="space-y-3 ml-2">
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-lilac-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed">Provide and maintain the QR code generation service</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-lilac-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed">Authenticate your identity and manage your account</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-lilac-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed">Save your QR code history so you can access past generations</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-lilac-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed">Respond to your inquiries submitted through the contact form</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-lilac-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed">Display usage statistics on your dashboard (total QR codes, scan counts)</p>
                    </div>
                </div>
            </div>

            <!-- Section 3 -->
            <div id="storage" class="mb-10">
                <h2 class="font-display text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0 text-sm font-bold text-brand-500">3</span>
                    Data Storage &amp; Security
                </h2>
                <p class="text-slate-500 text-sm leading-relaxed mb-4">
                    All data is stored locally on the server in an SQLite database. We implement the following security measures to protect your data:
                </p>
                <div class="space-y-3 ml-2">
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-mint-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Password Hashing:</span> All passwords are hashed using bcrypt before storage. We never store plain-text passwords.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-mint-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Session Security:</span> Session IDs are regenerated after login and registration to prevent session fixation attacks.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-mint-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Input Validation:</span> All user inputs are validated and sanitized to prevent injection attacks.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-mint-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Access Control:</span> Sensitive directories (config/, data/, includes/) are blocked from direct web access.</p>
                    </div>
                </div>
            </div>

            <!-- Section 4 -->
            <div id="thirdparty" class="mb-10">
                <h2 class="font-display text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0 text-sm font-bold text-brand-500">4</span>
                    Third-Party Services
                </h2>
                <p class="text-slate-500 text-sm leading-relaxed mb-4">
                    QRCode Pro uses the following third-party services delivered via CDN for frontend functionality:
                </p>
                <div class="space-y-3 ml-2">
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-sky-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Tailwind CSS</span> (cdn.tailwindcss.com) &mdash; for styling</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-sky-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Google Fonts</span> (fonts.googleapis.com) &mdash; for typography</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-sky-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Lucide Icons</span> (unpkg.com) &mdash; for icons</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-sky-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">QRCode.js / qr-code-styling</span> (cdn.jsdelivr.net) &mdash; for QR code generation</p>
                    </div>
                </div>
                <p class="text-slate-500 text-sm leading-relaxed mt-4">
                    These CDN providers may collect anonymous usage data as per their own privacy policies. <strong class="text-slate-700">We do not sell, trade, or share your personal data with any third party.</strong>
                </p>
            </div>

            <!-- Section 5 -->
            <div id="cookies" class="mb-10">
                <h2 class="font-display text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0 text-sm font-bold text-brand-500">5</span>
                    Cookies &amp; Sessions
                </h2>
                <p class="text-slate-500 text-sm leading-relaxed mb-4">
                    QRCode Pro uses a single PHP session cookie (PHPSESSID) to manage your login state. This cookie:
                </p>
                <div class="space-y-3 ml-2">
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-peach-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed">Is essential for authentication and cannot be disabled while logged in</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-peach-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed">Does not track you across websites</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-peach-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed">Is automatically deleted when you log out or close your browser</p>
                    </div>
                </div>
                <p class="text-slate-500 text-sm leading-relaxed mt-4">
                    We do not use any analytics cookies, advertising cookies, or tracking pixels.
                </p>
            </div>

            <!-- Section 6 -->
            <div id="rights" class="mb-10">
                <h2 class="font-display text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0 text-sm font-bold text-brand-500">6</span>
                    Your Rights
                </h2>
                <p class="text-slate-500 text-sm leading-relaxed mb-4">You have the right to:</p>
                <div class="space-y-3 ml-2">
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-rose-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Access</span> your personal data through your profile and dashboard</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-rose-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Update</span> your profile information (name, email) at any time</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-rose-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Delete</span> your individual QR codes from your history</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-rose-400 mt-2 flex-shrink-0"></div>
                        <p class="text-slate-500 text-sm leading-relaxed"><span class="font-semibold text-slate-700">Contact us</span> to request complete account deletion</p>
                    </div>
                </div>
            </div>

            <!-- Section 7 -->
            <div id="children" class="mb-10">
                <h2 class="font-display text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0 text-sm font-bold text-brand-500">7</span>
                    Children's Privacy
                </h2>
                <p class="text-slate-500 text-sm leading-relaxed">
                    QRCode Pro is not intended for use by children under the age of 13. We do not knowingly collect personal information from children. If you believe a child has provided us with personal data, please <a href="<?= url('/contact') ?>" class="text-brand-500 hover:text-brand-700 font-semibold transition-colors">contact us</a> and we will promptly remove it.
                </p>
            </div>

            <!-- Section 8 -->
            <div id="changes" class="mb-6">
                <h2 class="font-display text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0 text-sm font-bold text-brand-500">8</span>
                    Changes to This Policy
                </h2>
                <p class="text-slate-500 text-sm leading-relaxed">
                    We may update this Privacy Policy from time to time. Any changes will be reflected on this page with an updated "Last updated" date. We encourage you to review this policy periodically to stay informed about how we protect your information.
                </p>
            </div>

            <!-- Contact CTA -->
            <div class="rounded-2xl bg-gradient-to-r from-brand-50 to-lilac-50 p-6 border border-brand-100 mt-10">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-display font-bold text-slate-800 mb-1">Questions about your privacy?</h3>
                        <p class="text-sm text-slate-500">We're here to help. Reach out to us anytime.</p>
                    </div>
                    <a href="<?= url('/contact') ?>" class="px-6 py-2.5 rounded-xl font-display font-bold btn-primary text-sm flex items-center gap-2 flex-shrink-0">
                        Contact Us <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
