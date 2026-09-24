<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/config.php';
$pageTitle   = 'Ads Dashboard';
$currentPage = 'ads';

$metaConnected   = !empty(META_ACCESS_TOKEN) && !empty(META_AD_ACCOUNT_ID);
$googleConnected = !empty(GOOGLE_DEVELOPER_TOKEN) && !empty(GOOGLE_REFRESH_TOKEN);

// ── Fetch Meta Ads ────────────────────────────────────────────────────────────
$metaData = null;
if ($metaConnected) {
    $url  = "https://graph.facebook.com/v19.0/" . META_AD_ACCOUNT_ID . "/insights"
          . "?fields=spend,impressions,clicks,ctr,reach&date_preset=last_7d&access_token=" . META_ACCESS_TOKEN;
    $resp = @json_decode(@file_get_contents($url), true);
    if (!empty($resp['data'][0])) $metaData = $resp['data'][0];
}

// ── Fetch Google Ads ──────────────────────────────────────────────────────────
$googleData = null;
// Google Ads API requires OAuth — add keys in config.php and use Google Ads PHP client library

// ── Mock data when not connected ──────────────────────────────────────────────
$metaMock = [
    'spend' => '45800', 'impressions' => '284500', 'clicks' => '3920',
    'ctr'   => '1.38',  'reach' => '98400',
    'campaigns' => [
        ['name'=>'Summer Sale — Creams',   'spend'=>'18200','clicks'=>'1640','ctr'=>'1.52'],
        ['name'=>'Brand Awareness',         'spend'=>'14300','clicks'=>'1180','ctr'=>'1.21'],
        ['name'=>'Retargeting — Website',   'spend'=>'13300','clicks'=>'1100','ctr'=>'1.45'],
    ]
];
$googleMock = [
    'spend' => '32400', 'impressions' => '156800', 'clicks' => '2840',
    'ctr'   => '1.81',  'conversions' => '142',
    'campaigns' => [
        ['name'=>'ACM Brand Search',        'spend'=>'14100','clicks'=>'1380','ctr'=>'2.10'],
        ['name'=>'Product Shopping',        'spend'=>'10800','clicks'=>'860', 'ctr'=>'1.65'],
        ['name'=>'Display — Skincare',      'spend'=>'7500', 'clicks'=>'600', 'ctr'=>'1.42'],
    ]
];

$m = $metaData ?: $metaMock;
$g = $googleData ?: $googleMock;
$totalSpend = (float)$m['spend'] + (float)$g['spend'];

include __DIR__ . '/includes/header.php';
?>
<div style="display:flex;min-height:100vh">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<main class="main">
  <div class="topbar">
    <div>
      <div class="page-title">Ads Dashboard</div>
      <div class="page-sub">Meta + Google Ads Performance</div>
    </div>
    <div style="display:flex;gap:8px">
      <span style="font-size:12px;padding:5px 12px;border-radius:20px;background:<?= $metaConnected?'#F0FDF4':'#FFF7ED' ?>;color:<?= $metaConnected?'#166534':'#92400E' ?>;border:1px solid <?= $metaConnected?'#BBF7D0':'#FDE68A' ?>">
        <?= $metaConnected ? '● Meta Live' : '○ Meta Demo' ?>
      </span>
      <span style="font-size:12px;padding:5px 12px;border-radius:20px;background:<?= $googleConnected?'#F0FDF4':'#FFF7ED' ?>;color:<?= $googleConnected?'#166534':'#92400E' ?>;border:1px solid <?= $googleConnected?'#BBF7D0':'#FDE68A' ?>">
        <?= $googleConnected ? '● Google Live' : '○ Google Demo' ?>
      </span>
    </div>
  </div>

  <div class="content-section" style="padding-top:20px">
    <?php if (!$metaConnected || !$googleConnected): ?>
    <div class="alert alert-warning">
      ⚠️ Showing <strong>demo data</strong>. To connect live data:
      <?php if (!$metaConnected): ?><br>• <strong>Meta:</strong> Add META_ACCESS_TOKEN and META_AD_ACCOUNT_ID in config.php<?php endif; ?>
      <?php if (!$googleConnected): ?><br>• <strong>Google:</strong> Add GOOGLE_DEVELOPER_TOKEN and other keys in config.php<?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Combined Stats -->
    <div class="stats-grid" style="padding:0 0 16px">
      <div class="stat-card">
        <div class="stat-label">Total Spend <div class="stat-icon orange">💸</div></div>
        <div class="stat-value red-val">Rs. <?= number_format($totalSpend) ?></div>
        <div class="stat-change neutral">Meta + Google</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Total Impressions <div class="stat-icon blue">👁</div></div>
        <div class="stat-value"><?= number_format((float)$m['impressions'] + (float)$g['impressions']) ?></div>
        <div class="stat-change neutral">Last 7 days</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Total Clicks <div class="stat-icon green">👆</div></div>
        <div class="stat-value"><?= number_format((float)$m['clicks'] + (float)$g['clicks']) ?></div>
        <div class="stat-change neutral">Combined</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Avg CTR <div class="stat-icon blue">📈</div></div>
        <div class="stat-value"><?= number_format(((float)$m['ctr'] + (float)$g['ctr']) / 2, 2) ?>%</div>
        <div class="stat-change neutral">Meta + Google avg</div>
      </div>
    </div>

    <div class="content-grid">
      <!-- Meta Card -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">🔵 Meta Ads</div>
          <div class="card-badge">Rs. <?= number_format($m['spend']) ?> spent</div>
        </div>
        <div class="card-body">
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:20px">
            <div style="text-align:center;padding:12px;background:var(--bg);border-radius:8px">
              <div style="font-size:18px;font-weight:800;color:#1877F2"><?= number_format($m['impressions']) ?></div>
              <div style="font-size:11px;color:var(--muted)">Impressions</div>
            </div>
            <div style="text-align:center;padding:12px;background:var(--bg);border-radius:8px">
              <div style="font-size:18px;font-weight:800;color:#1877F2"><?= number_format($m['clicks']) ?></div>
              <div style="font-size:11px;color:var(--muted)">Clicks</div>
            </div>
            <div style="text-align:center;padding:12px;background:var(--bg);border-radius:8px">
              <div style="font-size:18px;font-weight:800;color:#1877F2"><?= number_format($m['ctr'], 2) ?>%</div>
              <div style="font-size:11px;color:var(--muted)">CTR</div>
            </div>
          </div>
          <div style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px">Campaigns</div>
          <?php foreach ($metaMock['campaigns'] as $c): ?>
          <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px">
            <span><?= htmlspecialchars($c['name']) ?></span>
            <div style="text-align:right">
              <div style="font-weight:700">Rs. <?= number_format($c['spend']) ?></div>
              <div style="font-size:11px;color:var(--muted)"><?= number_format($c['clicks']) ?> clicks · <?= $c['ctr'] ?>% CTR</div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Google Card -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">🔴 Google Ads</div>
          <div class="card-badge">Rs. <?= number_format($g['spend']) ?> spent</div>
        </div>
        <div class="card-body">
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:20px">
            <div style="text-align:center;padding:12px;background:var(--bg);border-radius:8px">
              <div style="font-size:18px;font-weight:800;color:#EA4335"><?= number_format($g['impressions']) ?></div>
              <div style="font-size:11px;color:var(--muted)">Impressions</div>
            </div>
            <div style="text-align:center;padding:12px;background:var(--bg);border-radius:8px">
              <div style="font-size:18px;font-weight:800;color:#EA4335"><?= number_format($g['clicks']) ?></div>
              <div style="font-size:11px;color:var(--muted)">Clicks</div>
            </div>
            <div style="text-align:center;padding:12px;background:var(--bg);border-radius:8px">
              <div style="font-size:18px;font-weight:800;color:#EA4335"><?= number_format($g['ctr'], 2) ?>%</div>
              <div style="font-size:11px;color:var(--muted)">CTR</div>
            </div>
          </div>
          <div style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px">Campaigns</div>
          <?php foreach ($googleMock['campaigns'] as $c): ?>
          <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px">
            <span><?= htmlspecialchars($c['name']) ?></span>
            <div style="text-align:right">
              <div style="font-weight:700">Rs. <?= number_format($c['spend']) ?></div>
              <div style="font-size:11px;color:var(--muted)"><?= number_format($c['clicks']) ?> clicks · <?= $c['ctr'] ?>% CTR</div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Meta Pixel Info -->
    <div class="card" style="margin-top:4px">
      <div class="card-header">
        <div class="card-title">🎯 Meta Pixel — Website Tracking</div>
        <span class="badge badge-<?= META_PIXEL_ID ? 'delivered' : 'processing' ?>"><?= META_PIXEL_ID ? 'Connected' : 'Not Connected' ?></span>
      </div>
      <div class="card-body">
        <?php if (!META_PIXEL_ID): ?>
        <div class="alert alert-warning" style="margin-bottom:14px">Add your META_PIXEL_ID in config.php to enable website tracking.</div>
        <?php endif; ?>
        <p style="font-size:13px;color:var(--muted);margin-bottom:12px">Meta Pixel tracks visitors on acmpvtltd.com — page views, product views, and purchases. Add this code to all pages of your website.</p>
        <div style="background:#0D2158;border-radius:8px;padding:14px;font-family:monospace;font-size:12px;color:#a8c4ff;overflow-x:auto">
          <pre style="margin:0;white-space:pre-wrap">&lt;!-- Meta Pixel - Add to &lt;head&gt; of every page --&gt;
&lt;script&gt;
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?= META_PIXEL_ID ?: 'YOUR_PIXEL_ID' ?>');
fbq('track', 'PageView');
&lt;/script&gt;
&lt;noscript&gt;&lt;img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?= META_PIXEL_ID ?: 'YOUR_PIXEL_ID' ?>&ev=PageView&noscript=1"/&gt;&lt;/noscript&gt;
&lt;!-- End Meta Pixel --&gt;</pre>
        </div>
      </div>
    </div>
  </div>
</main>
</div>
</body></html>
