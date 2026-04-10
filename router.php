<?php
/**
 * router.php — PHP Built-in Server Router
 *
 * Usage (run from inside the mr_qr_v1 directory):
 *   php -S localhost:8000 router.php
 *
 * This file is ONLY needed for the built-in dev server.
 * On Apache/Nginx the .htaccess handles routing automatically.
 */

$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri  = rtrim($uri, '/') ?: '/';

// Strip /mr_qr_v1 prefix if accessed with it (compatibility)
$route = preg_replace('#^/mr_qr_v1#', '', $uri) ?: '/';

// Serve real files (CSS, JS, images, etc.) directly
$realFile = __DIR__ . $route;
if ($route !== '/' && file_exists($realFile) && is_file($realFile)) {
    return false; // built-in server serves the file as-is
}

// Route map: clean path → actual PHP file
$routes = [
    '/'              => 'index.php',
    '/login'         => 'auth/login.php',
    '/register'      => 'auth/register.php',
    '/logout'        => 'auth/logout.php',
    '/profile'       => 'auth/profile.php',
    '/dashboard'     => 'dashboard.php',
    '/generate'      => 'generate.php',
    '/history'       => 'history.php',
    '/bulk'          => 'bulk.php',
    '/api/generate'  => 'api/generate.php',
    '/contact'       => 'contact.php',
    '/about'         => 'about.php',
    '/privacy'       => 'privacy.php',
    '/scanner'       => 'scanner.php',
];

if (isset($routes[$route])) {
    // Keep $_GET query string intact
    require __DIR__ . '/' . $routes[$route];
    exit;
}

// 404 fallback
http_response_code(404);
echo '<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>404 — QRCode Pro</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<style>body{font-family:"Plus Jakarta Sans",sans-serif;background:#FAFAFC;margin:0}h1,h2{font-family:"Outfit",sans-serif}</style></head>
<body class="min-h-screen flex items-center justify-center p-6">
<div class="text-center max-w-md">
<div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center mx-auto mb-8 shadow-lg"><i data-lucide="search-x" class="w-12 h-12 text-white"></i></div>
<h1 class="text-7xl font-extrabold text-slate-900 mb-2">404</h1>
<h2 class="text-xl font-bold text-slate-700 mb-3">Page Not Found</h2>
<p class="text-slate-500 mb-8 leading-relaxed">The page <code class="px-2 py-1 rounded-lg bg-slate-100 text-sm font-mono text-indigo-600">' . htmlspecialchars($route) . '</code> doesn\'t exist.</p>
<div class="flex flex-col sm:flex-row gap-3 justify-center">
<a href="/" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-indigo-500 to-indigo-600 shadow-md hover:shadow-lg transition-all text-sm"><i data-lucide="home" class="w-4 h-4"></i> Back to Home</a>
<a href="/generate" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-slate-700 bg-white border border-slate-200 hover:border-indigo-300 transition-all text-sm"><i data-lucide="plus-circle" class="w-4 h-4"></i> Generate QR</a>
</div></div>
<script>lucide.createIcons()</script></body></html>';
