<?php require 'config.php'; count_checkout(); $settings = get_data('settings'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Payment — FF TopUp</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<style>
/* ===== PAYMENT PAGE (scoped) ===== */
.pay-summary{background:linear-gradient(135deg,#e01e2b,#a11019);color:#fff;border-radius:16px;
  padding:18px;margin-bottom:20px;box-shadow:0 6px 18px rgba(224,30,43,.25)}
.ps-top{display:flex;align-items:center;gap:14px;padding-bottom:14px;border-bottom:1px solid rgba(255,255,255,.2)}
.ps-dia{width:48px;height:48px;flex-shrink:0}
.ps-info .ps-pack{font-size:19px;font-weight:800;line-height:1.1}
.ps-info .ps-sub{font-size:12.5px;opacity:.85;margin-top:2px}
.ps-amt{margin-left:auto;text-align:right}
.ps-amt .a-lbl{font-size:11px;opacity:.8}
.ps-amt .a-val{font-size:24px;font-weight:800;line-height:1}
.ps-bottom{display:flex;justify-content:space-between;padding-top:12px;font-size:13px}
.ps-bottom .pb-item .k{opacity:.8;font-size:11px}
.ps-bottom .pb-item .v{font-weight:700;font-size:13.5px}

.pay-heading{font-size:16px;font-weight:700;color:var(--ink);margin:0 0 14px 2px}

.method-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:8px}
.method-box{background:#fff;border:1.5px solid var(--line);border-radius:14px;padding:16px 12px;cursor:pointer;
  display:flex;align-items:center;gap:12px;transition:.15s;text-align:left}
.method-box:hover{border-color:var(--red);transform:translateY(-2px);box-shadow:0 6px 14px rgba(0,0,0,.06)}
.method-box:active{transform:scale(.97)}
.method-box img{height:32px;width:auto}
.method-box .mb-txt{font-weight:700;font-size:14px}
.method-box .mb-sub{font-size:11px;color:var(--muted);margin-top:1px}
.method-box.qr-method .qr-ic{width:36px;height:36px;background:#fff2f3;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.method-box.qr-method .qr-ic svg{width:22px;height:22px;fill:var(--red)}

.rzp-box{background:#fff;border:1px solid var(--line);border-radius:14px;padding:18px;display:flex;flex-direction:column;gap:12px}
.rzp-box input{width:100%;padding:13px;border:1.5px solid #ddd;border-radius:10px;font-size:15px;outline:none}
.rzp-box input:focus{border-color:var(--red)}
.rzp-box .btn-pay{background:var(--red);color:#fff;border:none;padding:15px;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer}

/* QR OVERLAY */
.qr-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);display:flex;align-items:center;justify-content:center;z-index:100;padding:16px}
.qr-modal{background:#fff;border-radius:18px;padding:26px 22px;max-width:340px;width:100%;text-align:center;position:relative;animation:pop .3s ease}
@keyframes pop{0%{opacity:0;transform:scale(.92)}100%{opacity:1;transform:none}}
.qr-modal h3{font-size:18px;color:var(--red);margin-bottom:6px}
.qr-modal .qm-sub{font-size:12.5px;color:var(--muted);margin-bottom:16px}
.qr-close{position:absolute;top:12px;right:14px;background:#f2f2f2;border:none;width:30px;height:30px;border-radius:50%;font-size:16px;color:var(--muted);cursor:pointer;line-height:1}
.qr-timer-box{display:inline-flex;align-items:center;gap:6px;background:#fff2f3;color:var(--red);font-weight:700;padding:7px 14px;border-radius:20px;font-size:13.5px;margin-bottom:16px}
.qr-timer-box svg{width:15px;height:15px;fill:var(--red)}
.qr-canvas-wrap{display:inline-block;background:#fff;padding:14px;border:2px solid var(--line);border-radius:14px}
#qrCanvas{display:block}
.qr-amt{font-size:20px;font-weight:800;margin:16px 0 4px}
.qr-amt-sub{font-size:12px;color:var(--muted);margin-bottom:16px}
.btn-download{background:#fff;border:2px solid var(--red);color:var(--red);padding:12px 22px;border-radius:11px;font-weight:700;cursor:pointer;font-size:14px;width:100%}
.btn-download:active{transform:scale(.97)}
.auto-verify{margin-top:14px;color:var(--muted);font-size:12.5px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:8px}
.pulse-dot{width:9px;height:9px;border-radius:50%;background:#1b9e4b;display:inline-block;animation:pulse 1.2s ease-in-out infinite}
@keyframes pulse{0%,100%{opacity:.35;transform:scale(.8)}50%{opacity:1;transform:scale(1.2)}}

@media(max-width:400px){.method-box{padding:14px 10px;gap:9px}.method-box img{height:28px}}
</style>
</head>
<body class="page-enter">

<header class="app-header">
  <div class="hdr-left">
    <a href="shop.php" class="back-btn">
      <svg viewBox="0 0 24 24" class="tag-svg"><path d="M15 5l-7 7 7 7"/></svg>
    </a>
  </div>
  <div class="hdr-center"><img src="assets/bisicon.png" alt="bis" class="hdr-icon-lg"></div>
  <div class="hdr-right"></div>
</header>

<main class="container">

  <!-- SUMMARY (with diamond icon) -->
  <div class="pay-summary">
    <div class="ps-top">
      <img src="assets/daimond_icon.png" class="ps-dia" alt="diamond">
      <div class="ps-info">
        <div class="ps-pack" id="sumPack">-</div>
        <div class="ps-sub">Free Fire Diamonds</div>
      </div>
      <div class="ps-amt">
        <div class="a-lbl">Amount</div>
        <div class="a-val" id="sumPrice">-</div>
      </div>
    </div>
    <div class="ps-bottom">
      <div class="pb-item"><div class="k">Player</div><div class="v" id="sumName">-</div></div>
      <div class="pb-item" style="text-align:right"><div class="k">UID</div><div class="v" id="sumUid">-</div></div>
    </div>
  </div>

  <!-- UPI MODE -->
  <section id="upiMode">
    <h2 class="pay-heading">Choose Payment Method</h2>
    <div class="method-grid">
      <button type="button" class="method-box" onclick="payApp('phonepe')">
        <img src="assets/phonepe.png" alt="PhonePe">
        <div><div class="mb-txt">PhonePe</div><div class="mb-sub">Pay via app</div></div>
      </button>
      <button type="button" class="method-box" onclick="payApp('paytm')">
        <img src="assets/paytm.png" alt="Paytm">
        <div><div class="mb-txt">Paytm</div><div class="mb-sub">Pay via app</div></div>
      </button>
      <button type="button" class="method-box" onclick="payApp('gpay')">
        <img src="assets/gpay.png" alt="GPay">
        <div><div class="mb-txt">GPay</div><div class="mb-sub">Pay via app</div></div>
      </button>
      <button type="button" class="method-box qr-method" onclick="openQr()">
        <span class="qr-ic">
          <svg viewBox="0 0 24 24"><path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm13-2h3v2h-3v-2zm-5 0h3v3h-2v-1h-1v-2zm5 5h3v3h-3v-1h-1v-1h1v-1zm-5 2h2v2h-2v-2z"/></svg>
        </span>
        <div><div class="mb-txt">Pay With QR</div><div class="mb-sub">Scan & pay</div></div>
      </button>
    </div>
  </section>

  <!-- RAZORPAY MODE -->
  <section id="rzpMode" class="hidden">
    <h2 class="pay-heading">Pay via Razorpay</h2>
    <div class="rzp-box">
      <input type="text" id="rzpUpi" placeholder="Enter your UPI ID (name@bank)">
      <input type="text" id="rzpTr" placeholder="Enter TR / Reference Code">
      <button type="button" class="btn-pay" onclick="payRazorpay()">Pay Now</button>
    </div>
  </section>

  <!-- QR OVERLAY -->
  <div id="qrOverlay" class="qr-overlay hidden">
    <div class="qr-modal">
      <button type="button" class="qr-close" onclick="closeQr()">✕</button>
      <h3>Scan &amp; Pay</h3>
      <p class="qm-sub">Open any UPI app and scan</p>
      <div class="qr-timer-box">
        <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 11h-4v-2h2V7h2v6z"/></svg>
        Expires in <span id="qrTimer">05:00</span>
      </div>
      <div class="qr-canvas-wrap"><canvas id="qrCanvas"></canvas></div>
      <div class="qr-amt" id="qrAmt">-</div>
      <div class="qr-amt-sub" id="qrPackName">-</div>
      <button type="button" class="btn-download" onclick="downloadQr()">Download QR</button>
      <p class="auto-verify"><span class="pulse-dot"></span> Automatic Payment Verification</p>
    </div>
  </div>
</main>

<footer class="app-footer">
  <img src="assets/garenalogo.png" alt="garena" class="footer-logo">
  <p class="footer-copy">© <?= date('Y') ?> FF Diamond TopUp.</p>
</footer>

<script>
const UPI_ID   = <?= json_encode($settings['upi_id'] ?? '') ?>;
const UPI_NAME = <?= json_encode($settings['upi_name'] ?? 'FF TopUp') ?>;
const RZP_KEY  = <?= json_encode($settings['razorpay_key'] ?? '') ?>;
const PAY_MODE = <?= json_encode($settings['payment_mode'] ?? 'all_upi') ?>;

const pack = JSON.parse(localStorage.getItem('ffPack') || '{}');
const acc  = JSON.parse(localStorage.getItem('ffAccount') || '{}');
const amount = parseInt(pack.offer || 0);

document.getElementById('sumPack').textContent  = (pack.diamonds || '-') + ' Diamonds';
document.getElementById('sumName').textContent  = acc.Name || '-';
document.getElementById('sumUid').textContent   = acc.UID || '-';
document.getElementById('sumPrice').textContent = '₹' + amount;

if (PAY_MODE === 'razorpay') {
  document.getElementById('upiMode').classList.add('hidden');
  document.getElementById('rzpMode').classList.remove('hidden');
}

function upiParams(){
  return 'pa=' + encodeURIComponent(UPI_ID) +
         '&pn=' + encodeURIComponent(UPI_NAME) +
         '&am=' + amount + '&cu=INR' +
         '&tn=' + encodeURIComponent('FF ' + (pack.diamonds || '') + ' Diamonds');
}

function payApp(app){
  const p = upiParams();
  let link;
  if (app === 'phonepe') link = 'phonepe://pay?' + p;
  else if (app === 'paytm') link = 'paytmmp://pay?' + p;
  else if (app === 'gpay') link = 'tez://upi/pay?' + p;
  else link = 'upi://pay?' + p;
  window.location.href = link;
  setTimeout(function(){ window.location.href = 'upi://pay?' + p; }, 2500);
}

let timerInt;
function openQr(){
  const ov = document.getElementById('qrOverlay');
  ov.classList.remove('hidden');
  document.getElementById('qrAmt').textContent = '₹' + amount;
  document.getElementById('qrPackName').textContent = (pack.diamonds || '') + ' Diamonds';

  const canvas = document.getElementById('qrCanvas');
  // clear old QR
  const ctx = canvas.getContext('2d');
  if (ctx) ctx.clearRect(0,0,canvas.width,canvas.height);

  QRCode.toCanvas(canvas, 'upi://pay?' + upiParams(), { width: 210, margin: 2 }, function(err){
    if (err) console.log(err);
  });
  startTimer(300);
}
function closeQr(){
  document.getElementById('qrOverlay').classList.add('hidden');
  clearInterval(timerInt);
}
function startTimer(sec){
  clearInterval(timerInt);
  const el = document.getElementById('qrTimer');
  function tick(){
    if (sec <= 0){ clearInterval(timerInt); el.textContent = 'Expired'; return; }
    const m = String(Math.floor(sec/60)).padStart(2,'0');
    const s = String(sec%60).padStart(2,'0');
    el.textContent = m + ':' + s;
    sec--;
  }
  tick();
  timerInt = setInterval(tick, 1000);
}
function downloadQr(){
  const c = document.getElementById('qrCanvas');
  const a = document.createElement('a');
  a.href = c.toDataURL('image/png');
  a.download = 'payment-qr.png';
  a.click();
}
function payRazorpay(){
  const upi = document.getElementById('rzpUpi').value.trim();
  const tr  = document.getElementById('rzpTr').value.trim();
  if (!upi || !tr){ alert('Please enter both UPI ID and TR code'); return; }
  new Razorpay({
    key: RZP_KEY, amount: amount*100, currency: 'INR',
    name: UPI_NAME, description: (pack.diamonds || '') + ' Diamonds',
    prefill: { vpa: upi }, notes: { tr_code: tr, uid: acc.UID || '' },
    theme: { color: '#e01e2b' },
    handler: function(r){ alert('Payment success: ' + r.razorpay_payment_id); }
  }).open();
}
</script>
</body>
</html>
