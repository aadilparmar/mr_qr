<?php
// ── Logic BEFORE any HTML output ──────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$subject || !$message) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($message) < 10) {
        $error = 'Message must be at least 10 characters';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        setFlash('success', 'Your message has been sent! We will get back to you soon.');
        header('Location: ' . url('/contact'));
        exit;
    }
}

// ── Now safe to output HTML ──────────────────────────────────────────────────
$pageTitle = 'Contact Us';
require_once __DIR__ . '/includes/header.php';
?>

<section class="relative overflow-hidden hero-gradient">
    <div class="absolute top-20 left-16 w-16 h-16 rounded-2xl bg-brand-200/30 rotate-12 animate-float" style="animation-delay:0s"></div>
    <div class="absolute top-32 right-24 w-12 h-12 rounded-full bg-lilac-200/30 animate-float" style="animation-delay:1.5s"></div>
    <div class="absolute bottom-20 left-1/3 w-14 h-14 rounded-xl bg-sky-200/30 -rotate-12 animate-float" style="animation-delay:0.8s"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16 md:py-20 relative z-10 text-center">
        <p class="text-sm font-semibold text-brand-500 uppercase tracking-wider mb-3 animate-fade-in-up">Get in Touch</p>
        <h1 class="font-display text-4xl md:text-5xl font-extrabold text-slate-900 mb-4 animate-fade-in-up delay-100">Contact Us</h1>
        <p class="text-lg text-slate-500 max-w-2xl mx-auto animate-fade-in-up delay-200">Have questions, feedback, or just want to say hello? We'd love to hear from you.</p>
    </div>
</section>

<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid md:grid-cols-5 gap-8">

            <!-- Left: Contact Info -->
            <div class="md:col-span-2 space-y-5">
                <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 card-hover animate-fade-in-up">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i data-lucide="mail" class="w-7 h-7 text-brand-500"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-slate-800 mb-1">Email Us</h3>
                            <p class="text-sm text-slate-500">qrcodepro.help@gmail.com</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 card-hover animate-fade-in-up delay-100">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-xl bg-lilac-50 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i data-lucide="map-pin" class="w-7 h-7 text-lilac-500"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-slate-800 mb-1">Location</h3>
                            <p class="text-sm text-slate-500">Marwadi University<br>Rajkot, Gujarat, India</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 card-hover animate-fade-in-up delay-200">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-xl bg-mint-50 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i data-lucide="clock" class="w-7 h-7 text-mint-500"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-slate-800 mb-1">Response Time</h3>
                            <p class="text-sm text-slate-500">We typically respond within 24 hours</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-card border border-slate-100 card-hover animate-fade-in-up delay-300">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-xl bg-sky-50 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i data-lucide="github" class="w-7 h-7 text-sky-500"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-slate-800 mb-1">Open Source</h3>
                            <p class="text-sm text-slate-500">Report issues or contribute on GitHub</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Contact Form -->
            <div class="md:col-span-3">
                <div class="bg-white rounded-3xl p-8 shadow-card border border-slate-100 animate-fade-in-up delay-100">
                    <h2 class="font-display font-bold text-xl text-slate-900 mb-6 flex items-center gap-2">
                        <i data-lucide="send" class="w-5 h-5 text-brand-500"></i> Send a Message
                    </h2>

                    <?php if ($error): ?>
                    <div class="rounded-xl p-4 flex items-center gap-3 mb-6 bg-rose-50 border border-rose-200">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 flex-shrink-0"></i>
                        <span class="text-sm font-medium text-slate-700"><?= htmlspecialchars($error) ?></span>
                    </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Your Name <span class="text-rose-400">*</span></label>
                                <input type="text" name="name" required
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                                       class="input-field w-full px-4 py-3"
                                       placeholder="John Doe">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address <span class="text-rose-400">*</span></label>
                                <input type="email" name="email" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       class="input-field w-full px-4 py-3"
                                       placeholder="john@example.com">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Subject <span class="text-rose-400">*</span></label>
                            <input type="text" name="subject" required
                                   value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>"
                                   class="input-field w-full px-4 py-3"
                                   placeholder="How can we help?">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Message <span class="text-rose-400">*</span></label>
                            <textarea name="message" required rows="6"
                                      class="input-field w-full px-4 py-3"
                                      placeholder="Write your message here..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="px-8 py-3.5 rounded-xl font-display font-bold btn-primary text-sm flex items-center gap-2">
                            Send Message <i data-lucide="send" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
