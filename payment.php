<?php require 'config.php'; count_checkout();
bump_stat('checkouts');
$settings = get_data('settings');
$upi_id   = $settings['upi_id'];
$upi_name = $settings['upi_name'];
$mode     = $settings['payment_mode'];
$rzp_key  = $settings['razorpay_key'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Payment — FF Diamonds</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<?php if($mode==='razorpay'): ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<?php endif; ?>
</head>
<body class="page-enter">

<header class="site-header">
  <div class="hdr-inner">
    <div class="hdr-left"><a href="shop.php" class="back-link">
      <svg viewBox="0 0 24 24" class="tag-svg"><path d="M15 18l-6-6 6-6"/></svg> Back
    </a></div>
    <div class="hdr-center"><img src="assets/bisicon.png" alt="BIS" class="hdr-ico-lg"></div>
    <div class="hdr-right"></div>
  </div>
</header>

<main class="wrap">

  <!-- Summary -->
  <section class="summary-card">
    <div class="sum-row"><span>Pack</span><span id="sPack">—</span></div>
    <div class="sum-row"><span>Player</span><span id="sName">—</span></div>
    <div class="sum-row"><span>UID</span><span id="sUid">—</span></div>
    <div class="sum-row total"><span>Amount</span><span id="sAmt">—</span></div>
  </section>

  <h2 class="sec-title center">Choose Payment Method</h2>

  <?php if($mode==='allupi'): ?>
  <!-- ALL UPI MODE -->
  <div class="pay-apps">
    <button class="pay-app" onclick="payApp('phonepe')"><img src="assets/phonepe.png"><span>PhonePe</span></button>
    <button class="pay-app" onclick="payApp('paytm')"><img src="assets/paytm.png"><span>Paytm</span></button>
    <button class="pay-app" onclick="payApp('gpay')"><img src="assets/gpay.png"><span>GPay</span></button>
  </div>
  <button class="btn-red full" onclick="showQr()">Pay With QR</button>

  <div id="qrBox" class="qr-box hide">
    <div class="qr-timer">Time left: <span id="timer">05:00</span></div>
    <div class="qr-frame"><canvas id="qrCanvas"></canvas></div>
    <button class="btn-outline" onclick="downloadQr()">Download QR</button>
    <p class="auto-verify">
      <svg viewBox="0 0 24 24" class="tag-svg spin-slow"><path d="M12 4V1L8 5l4 4V6a6 6 0 11-6 6H4a8 8 0 108-8z"/></svg>
      Automatic Payment Verification
    </p>
  </div>

  <?php else: ?>
  <!-- RAZORPAY MODE -->
  <div class="rzp-box">
    <label class="fld-lbl">Your UPI ID</label>
    <input type="text" id="rzpUpi" class="fld" placeholder="yourname@upi">
    <label class="fld-lbl">Enter TR / Transaction Code</label>
    <input type="text" id="rzpTr" class="fld" placeholder="Transaction reference code">
    <button class="btn-red full" onclick="payRazorpay()">Pay Now</button>
    <p class="auto-verify">Secured by Razorpay</p>
  </div>
  <?php endif; ?>

</main>

<footer class="site-footer">
  <img src="assets/garenalogo.png" alt="Garena" class="foot-logo">
  <p class="foot-copy">© <?php echo date('Y'); ?> FF Diamond TopUp.</p>
</footer>

<script>
const UPI_ID   = <?php echo json_encode($upi_id); ?>;
const UPI_NAME = <?php echo json_encode($upi_name); ?>;
const RZP_KEY  = <?php echo json_encode($rzp_key); ?>;

const pack = JSON.parse(localStorage.getItem('ffPack')||'{}');
const acc  = JSON.parse(localStorage.getItem('ffAccount')||'{}');
const amount = pack.offer || 0;

document.getElementById('sPack').textContent = (pack.diamonds||'—')+' Diamonds';
document.getElementById('sName').textContent = acc.Name||'—';
document.getElementById('sUid').textContent  = acc.UID||'—';
document.getElementById('sAmt').textContent  = '₹'+amount;

function upiLink(app){
  const base = `upi://pay?pa=${encodeURIComponent(UPI_ID)}&pn=${encodeURIComponent(UPI_NAME)}&am=${amount}&cu=INR&tn=${encodeURIComponent('FF '+(pack.diamonds||'')+' Diamonds')}`;
  return base;
}
function payApp(app){ window.location.href = upiLink(app); }

let timerInt;
function showQr(){
  const box=document.getElementById('qrBox');
  box.classList.remove('hide');
  box.scrollIntoView({behavior:'smooth'});
  QRCode.toCanvas(document.getElementById('qrCanvas'), upiLink('qr'), {width:220, margin:1}, ()=>{});
  startTimer(300);
}
function startTimer(sec){
  clearInterval(timerInt);
  const el=document.getElementById('timer');
  timerInt=setInterval(()=>{
    if(sec<=0){clearInterval(timerInt); el.textContent='Expired'; return;}
    sec--; const m=String(Math.floor(sec/60)).padStart(2,'0');
    const s=String(sec%60).padStart(2,'0'); el.textContent=`${m}:${s}`;
  },1000);
}
function downloadQr(){
  const c=document.getElementById('qrCanvas');
  const a=document.createElement('a');
  a.href=c.toDataURL('image/png'); a.download='payment-qr.png'; a.click();
}
function payRazorpay(){
  const upi=document.getElementById('rzpUpi').value.trim();
  const tr=document.getElementById('rzpTr').value.trim();
  if(!upi||!tr){ alert('Please enter both UPI ID and TR code'); return; }
  const opt={
    key: RZP_KEY, amount: amount*100, currency:'INR',
    name: UPI_NAME, description: (pack.diamonds||'')+' Diamonds',
    prefill:{ vpa: upi }, notes:{ tr_code: tr, uid: acc.UID||'' },
    theme:{ color:'#e01e2b' },
    handler:function(r){ alert('Payment success: '+r.razorpay_payment_id); }
  };
  const rz=new Razorpay(opt); rz.open();
}
</script>
</body>
</html>
