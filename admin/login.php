<?php /* DEPLOY-CHECK-ABC123 */
session_start();
require_once __DIR__ . '/config.php';

if (!empty($_SESSION['acm_admin'])) {
    header('Location: /admin'); exit;
}

// Rate limiting: 5 attempts → 10 min block
$_ATTEMPTS_FILE = __DIR__ . '/.login_attempts.json';
function getAttempts($f) {
    if (!file_exists($f)) return [];
    $d = json_decode(file_get_contents($f), true) ?: [];
    foreach ($d as $ip => $v) { if (time() - $v['t'] > 600) unset($d[$ip]); }
    return $d;
}
function saveAttempts($f, $d) { file_put_contents($f, json_encode($d)); }
$_CLIENT_IP = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '')[0]);
$_ATTEMPTS  = getAttempts($_ATTEMPTS_FILE);
$_BLOCKED   = isset($_ATTEMPTS[$_CLIENT_IP]) && $_ATTEMPTS[$_CLIENT_IP]['c'] >= 5
              ? max(0, 600 - (time() - $_ATTEMPTS[$_CLIENT_IP]['t'])) : 0;

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if ($_BLOCKED > 0) {
        $error = 'Too many attempts. Try again in ' . ceil($_BLOCKED / 60) . ' min.';
    } elseif ($u === ADMIN_USERNAME && $p === ADMIN_PASSWORD) {
        unset($_ATTEMPTS[$_CLIENT_IP]);
        saveAttempts($_ATTEMPTS_FILE, $_ATTEMPTS);
        $_SESSION['acm_admin']     = true;
        $_SESSION['acm_user']      = $u;
        $_SESSION['last_activity'] = time();
        header('Location: /admin'); exit;
    } else {
        if (!isset($_ATTEMPTS[$_CLIENT_IP]) || time() - $_ATTEMPTS[$_CLIENT_IP]['t'] > 600) {
            $_ATTEMPTS[$_CLIENT_IP] = ['c' => 1, 't' => time()];
        } else {
            $_ATTEMPTS[$_CLIENT_IP]['c']++;
        }
        saveAttempts($_ATTEMPTS_FILE, $_ATTEMPTS);
        $left  = 5 - $_ATTEMPTS[$_CLIENT_IP]['c'];
        $error = $left > 0 ? "Wrong credentials. $left attempt" . ($left > 1 ? 's' : '') . " left." : 'Blocked for 10 minutes.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — ACM Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Poppins',sans-serif;min-height:100vh;display:flex;background:#f0f2f8}
.login-wrap{display:flex;width:100%;min-height:100vh}

/* Left panel */
.login-left{width:420px;background:#0D2258;display:flex;flex-direction:column;justify-content:center;padding:60px 48px;flex-shrink:0}
.ll-logo{width:72px;height:72px;object-fit:contain;filter:brightness(0) invert(1);margin-bottom:28px}
.ll-name{font-size:28px;font-weight:800;color:#fff;margin-bottom:6px}
.ll-sub{font-size:13px;color:rgba(255,255,255,.55);margin-bottom:32px}
.ll-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:8px;padding:8px 16px;color:rgba(255,255,255,.7);font-size:12px;font-weight:600}
.ll-features{margin-top:48px;display:flex;flex-direction:column;gap:14px}
.ll-feat{display:flex;align-items:center;gap:12px;color:rgba(255,255,255,.55);font-size:12px}
.ll-feat-dot{width:6px;height:6px;background:#DC2626;border-radius:50%;flex-shrink:0}

/* Right panel */
.login-right{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 24px}
.login-card{background:#fff;border-radius:20px;padding:44px 40px;width:100%;max-width:420px;box-shadow:0 8px 40px rgba(13,34,88,.10)}
.lc-logo-row{display:flex;align-items:center;gap:12px;margin-bottom:32px}
.lc-logo-row img{width:44px;height:44px;object-fit:contain}
.lc-logo-name{font-size:16px;font-weight:800;color:#0D2258}
.lc-logo-sub{font-size:11px;color:#94a3b8}
.lc-title{font-size:22px;font-weight:800;color:#0D2258;margin-bottom:4px}
.lc-sub{font-size:13px;color:#94a3b8;margin-bottom:28px}
label{display:block;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
input[type=text],input[type=password]{width:100%;padding:12px 16px;border:1.5px solid #e2e8f0;border-radius:10px;font-family:'Poppins',sans-serif;font-size:14px;color:#1e293b;outline:none;transition:.15s;margin-bottom:18px}
input:focus{border-color:#0D2258}
.btn-login{width:100%;padding:13px;background:#DC2626;color:#fff;border:none;border-radius:10px;font-family:'Poppins',sans-serif;font-size:14px;font-weight:700;cursor:pointer;transition:.15s;margin-top:6px}
.btn-login:hover{background:#b91c1c}
.alert-err{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:8px;padding:10px 14px;font-size:13px;margin-bottom:18px}
.login-footer{margin-top:24px;padding-top:18px;border-top:1px solid #f1f5f9;text-align:center;font-size:11px;color:#94a3b8}
.login-footer a{color:#DC2626;text-decoration:none}

@media(max-width:700px){
  .login-left{display:none}
  .login-right{padding:24px 16px}
  .login-card{padding:32px 24px}
}
</style>
</head>
<body>
<div class="login-wrap">

  <!-- Left branding -->
  <div class="login-left">
    <img src="https://acmpvtltd.com/logo.png" alt="ACM" class="ll-logo">
    <div class="ll-name">ACM Admin</div>
    <div class="ll-sub">Asia Cosmetics & Manufactures Pvt. Ltd.</div>
    <div class="ll-badge">🔒 Secure Admin Portal</div>
    <div class="ll-features">
      <div class="ll-feat"><span class="ll-feat-dot"></span>Orders & Client Management</div>
      <div class="ll-feat"><span class="ll-feat-dot"></span>Products & Stock Control</div>
      <div class="ll-feat"><span class="ll-feat-dot"></span>Revenue Analytics</div>
      <div class="ll-feat"><span class="ll-feat-dot"></span>WhatsApp Quick Actions</div>
    </div>
  </div>

  <!-- Right login form -->
  <div class="login-right">
    <div class="login-card">

      <div class="lc-logo-row">
        <img src="https://acmpvtltd.com/logo.png" alt="ACM">
        <div>
          <div class="lc-logo-name">ACM Admin</div>
          <div class="lc-logo-sub">Asia Cosmetics & Manufactures</div>
        </div>
      </div>

      <div class="lc-title">Welcome back</div>
      <div class="lc-sub">Sign in to your admin panel</div>

      <?php if ($error): ?>
      <div class="alert-err">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter username" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>
        <button type="submit" class="btn-login">Sign In →</button>
      </form>

      <div class="login-footer">
        ACM Pvt Ltd &nbsp;·&nbsp; <a href="https://acmpvtltd.com" target="_blank">acmpvtltd.com</a>
      </div>
    </div>
  </div>

</div>
</body>
</html>
