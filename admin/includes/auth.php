<?php
if (session_status() === PHP_SESSION_NONE) session_start();

define('SESSION_TIMEOUT', 1800); // 30 minutes

if (isset($_SESSION['acm_admin'])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        session_destroy();
        header('Location: /admin/login?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
} elseif (empty($_SESSION['acm_admin'])) {
    header('Location: /admin/login');
    exit;
}

// ── Shared helpers ───────────────────────────────────────────
function clean($v) { return htmlspecialchars(trim($v ?? ''), ENT_QUOTES, 'UTF-8'); }

function waNumber($phone) {
    $n = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($n) < 7) return '';
    if (substr($n, 0, 2) === '92') return $n;
    if (substr($n, 0, 1) === '0')  return '92' . substr($n, 1);
    if (strlen($n) === 10)          return '92' . $n;
    return '92' . $n;
}
