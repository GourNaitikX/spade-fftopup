<?php require 'config.php'; count_checkout(); $settings = get_data('settings'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Payment — FF Diamonds</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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

  <!-- SUMMARY -->
  <div class="summary-card">
    <div class="sum-row"><span>Pack</span><b id="sumPack">-</b></div>
    <div class="sum-row"><span>Player</span><b id="sumName">-</b></div>
    <div class="sum-row"><span>UID</span><b id="sumUid">-</b></div>
    <div class="sum-row"><span>Amount</span><b id="sumPrice">-</b></div>
  </div>

  <!-- UPI APP MODE -->
  <section id="upiMode">
    <h2 class="section-title">Choose Payment Method</h2>
    <div class="pay-apps">
      <button type="button" class="pay-app" onclick="payApp('phonepe')"><img src="assets/phonepe.png" alt="PhonePe"><span>PhonePe</span></button>
      <button type="button" class="pay-app" onclick="payApp('paytm')"><img src="assets/paytm.png" alt="Paytm"><span>Paytm</span></button>
      <button type="button" class="pay-app" onclick="payApp('gpay')"><img src="assets/gpay.png" alt="GPay"><span>GPay</span></button>
    </div>
    <button type="button" id="payQrBtn" class="btn-qr" onclick="openQr()">Pay With QR</button>
  </section>

  <!-- RAZORPAY MODE -->
  <section id="rzpMode" class="hidden">
    <h2 class="section-title">Pay via Razorpay</h2>
    <div class="rzp-box">
      <input type="text" id="rzpUpi" placeholder="Enter your UPI ID (name@bank)">
      <input type="text" id="rzpTr" placeholder="Enter TR / Reference Code">
      <button type="button" id="rzpPayBtn" class="btn-qr" onclick="payRazorpay()">Pay Now</button>
    </div>
  </section>

  <!-- QR OVERLAY -->
  <div id="qrOverlay" class="qr-overlay hidden">
    <div class="qr-modal">
      <button type="button" class="qr-close" onclick="closeQr()">✕</button>
      <h3 class="section-title">Scan &amp; Pay</h3>
      <div class="qr-timer">Expires in <span id="qrTimer">05:00</span></div>
      <div class="qr-canvas-wrap"><canvas id="qrCanvas"></canvas></div>
      <p class="qr-amt" id="qrAmt"></p>
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

// switch mode
if (PAY_MODE === 'razorpay') {
  document.getElementById('upiMode').classList.add('hidden');
  document.getElementById('rzpMode').classList.remove('hidden');
}

function upiLink() {
  return `upi://pay?pa=${encodeURIComponent(UPI_ID)}&pn=${encodeURIComponent(UPI_NAME)}&am=${amount}&cu=INR&tn=${encodeURIComponent('FF ' + (pack.diamonds || '') + ' Diamonds')}`;
}
function payApp() { window.location.href = upiLink(); }

let timerInt;
function openQr() {
  const ov = document.getElementById('qrOverlay');
  ov.classList.remove('hidden');
  document.getElementById('qrAmt').textContent = '₹' + amount;
  const canvas = document.getElementById('qrCanvas');
  QRCode.toCanvas(canvas, upiLink(), { width: 210, margin: 2 }, function(){});
  startTimer(300);
}
function closeQr() {
  document.getElementById('qrOverlay').classList.add('hidden');
  clearInterval(timerInt);
}
function startTimer(sec) {
  clearInterval(timerInt);
  const el = document.getElementById('qrTimer');
  timerInt = setInterval(() => {
    if (sec <= 0) { clearInterval(timerInt); el.textContent = 'Expired'; return; }
    sec--;
    const m = String(Math.floor(sec / 60)).padStart(2, '0');
    const s = String(sec % 60).padStart(2, '0');
    el.textContent = `${m}:${s}`;
  }, 1000);
}
function downloadQr() {
  const c = document.getElementById('qrCanvas');
  const a = document.createElement('a');
  a.href = c.toDataURL('image/png');
  a.download = 'payment-qr.png';
  a.click();
}
function payRazorpay() {
  const upi = document.getElementById('rzpUpi').value.trim();
  const tr  = document.getElementById('rzpTr').value.trim();
  if (!upi || !tr) { alert('Please enter both UPI ID and TR code'); return; }
  const opt = {
    key: RZP_KEY, amount: amount * 100, currency: 'INR',
    name: UPI_NAME, description: (pack.diamonds || '') + ' Diamonds',
    prefill: { vpa: upi }, notes: { tr_code: tr, uid: acc.UID || '' },
    theme: { color: '#e01e2b' },
    handler: function(r) { alert('Payment success: ' + r.razorpay_payment_id); }
  };
  new Razorpay(opt).open();
}
</script>
</body>
</html>
