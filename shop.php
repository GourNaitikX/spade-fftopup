<?php
require 'config.php';
$packs = get_data('packs');
function offpercent($o,$of){ if($o<=0) return 0; return round((($o-$of)/$o)*100); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Shop — FF Diamonds</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="page-enter">

<header class="site-header">
  <div class="hdr-inner">
    <div class="hdr-left"><a href="index.php" class="back-link">
      <svg viewBox="0 0 24 24" class="tag-svg"><path d="M15 18l-6-6 6-6"/></svg> Back
    </a></div>
    <div class="hdr-center"><img src="assets/bisicon.png" alt="BIS" class="hdr-ico-lg"></div>
    <div class="hdr-right"></div>
  </div>
</header>

<main class="wrap">
  <div class="acct-strip" id="acctStrip">
    <img src="assets/avatar.png" class="acct-av">
    <div>
      <p class="acct-name" id="acctName">Player</p>
      <p class="acct-uid" id="acctUid">UID: —</p>
    </div>
  </div>

  <h2 class="sec-title center">Select a Diamond Pack</h2>

  <div class="pack-grid">
    <?php foreach($packs as $p): $off = offpercent($p['original'],$p['offer']); ?>
    <div class="pack-card"
         data-id="<?php echo $p['id'];?>"
         data-dia="<?php echo $p['diamonds'];?>"
         data-orig="<?php echo $p['original'];?>"
         data-offer="<?php echo $p['offer'];?>">
      <div class="off-tag"><?php echo $off;?>% OFF</div>
      <img src="assets/daimond_icon.png" class="pack-dia" alt="Diamond">
      <div class="pack-count"><?php echo $p['diamonds'];?></div>
      <div class="pack-sub">Diamonds</div>
      <button class="price-btn" onclick="pickPack(this)">
        <span class="p-orig">₹<?php echo $p['original'];?></span>
        <span class="p-offer">₹<?php echo $p['offer'];?></span>
      </button>
    </div>
    <?php endforeach; ?>
  </div>
</main>

<footer class="site-footer">
  <img src="assets/garenalogo.png" alt="Garena" class="foot-logo">
  <p class="foot-copy">© <?php echo date('Y'); ?> FF Diamond TopUp.</p>
</footer>

<script>
// load verified account
const acc = JSON.parse(localStorage.getItem('ffAccount')||'{}');
if(acc.UID){ document.getElementById('acctName').textContent=acc.Name||'Player';
  document.getElementById('acctUid').textContent='UID: '+acc.UID; }
else { document.getElementById('acctStrip').style.display='none'; }

function pickPack(btn){
  const c = btn.closest('.pack-card');
  const pack = {
    id:c.dataset.id, diamonds:c.dataset.dia,
    original:c.dataset.orig, offer:c.dataset.offer
  };
  localStorage.setItem('ffPack', JSON.stringify(pack));
  document.body.classList.add('page-leave');
  setTimeout(()=>location.href='payment.php', 350);
}
</script>
</body>
</html>