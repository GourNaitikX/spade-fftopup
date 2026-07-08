<?php require 'config.php'; count_click(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>FF Diamond TopUp</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- HEADER -->
<header class="app-header">
  <div class="hdr-left"><img src="assets/ffmax_icon.png" alt="ffmax" class="hdr-icon"></div>
  <div class="hdr-center"><img src="assets/bisicon.png" alt="bis" class="hdr-icon-lg"></div>
  <div class="hdr-right"></div>
</header>

<main class="container">

  <!-- BANNER -->
  <section class="banner-wrap">
    <img src="assets/banner.png" alt="banner" class="banner-img">
  </section>

  <!-- TAGS -->
  <div class="tags-row">
    <span class="tag">
      <svg viewBox="0 0 24 24" class="tag-svg"><path d="M13 2 3 14h7l-1 8 10-12h-7z"/></svg>
      Fast Checkout
    </span>
    <span class="tag">
      <svg viewBox="0 0 24 24" class="tag-svg"><path d="M21 7 9 19l-5.5-5.5 1.4-1.4L9 16.2 19.6 5.6z"/></svg>
      Instant Delivery
    </span>
  </div>

  <!-- FF LOGO -->
  <div class="fflogo-wrap"><img src="assets/fflogo.png" alt="ff logo" class="fflogo"></div>

  <!-- VERIFY SECTION -->
  <section class="verify-section">
    <h2 class="section-title">Verify Your UID</h2>

    <div class="uid-box">
      <input type="text" id="uidInput" inputmode="numeric" placeholder="Enter your Free Fire UID" autocomplete="off">
      <button id="verifyBtn" class="btn-verify">Verify</button>
    </div>
    <p id="uidError" class="uid-error"></p>

    <!-- LOADING -->
    <div id="loadingBox" class="loading-box hidden">
      <img src="assets/fficon.png" class="loading-icon" alt="loading">
      <p class="loading-text">Verifying your account...</p>
    </div>

    <!-- RESULT CARD -->
    <div id="resultCard" class="result-card hidden">
      <div class="avatar-wrap"><img src="assets/avatar.png" alt="avatar" class="avatar-img"></div>
      <h3 id="rName" class="r-name"></h3>

      <div class="info-grid">
        <div class="info-item">
          <svg viewBox="0 0 24 24" class="info-svg"><path d="M12 2 4 6v6c0 5 3.4 8.7 8 10 4.6-1.3 8-5 8-10V6z"/></svg>
          <span class="lbl">UID</span><span id="rUid" class="val"></span>
        </div>
        <div class="info-item">
          <svg viewBox="0 0 24 24" class="info-svg"><path d="M12 2 15 9l7 .6-5.3 4.6L18.3 21 12 17.3 5.7 21l1.6-6.8L2 9.6 9 9z"/></svg>
          <span class="lbl">Level</span><span id="rLevel" class="val"></span>
        </div>
        <div class="info-item">
          <svg viewBox="0 0 24 24" class="info-svg"><path d="M12 21s-7-4.5-9.5-9C.9 8.6 2.5 5 6 5c2 0 3.2 1.2 4 2.3C10.8 6.2 12 5 14 5c3.5 0 5.1 3.6 3.5 7-2.5 4.5-9.5 9-9.5 9z"/></svg>
          <span class="lbl">Likes</span><span id="rLikes" class="val"></span>
        </div>
        <div class="info-item">
          <svg viewBox="0 0 24 24" class="info-svg"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 2c1.7 0 3.7 2.9 4 8H8c.3-5.1 2.3-8 4-8zm-4 10h8c-.3 5.1-2.3 8-4 8s-3.7-2.9-4-8z"/></svg>
          <span class="lbl">Region</span><span id="rRegion" class="val"></span>
        </div>
      </div>

      <button id="continueBtn" class="btn-continue">Continue Shopping</button>
    </div>
  </section>
</main>

<!-- FOOTER -->
<footer class="app-footer">
  <img src="assets/garenalogo.png" alt="garena" class="footer-logo">
  <p class="footer-note">This is an independent top-up store. Not affiliated with Garena.</p>
  <p class="footer-copy">© <?= date('Y') ?> FF Diamond TopUp. All rights reserved.</p>
</footer>

<div id="confetti" class="confetti-layer"></div>

<script>
const verifyBtn  = document.getElementById('verifyBtn');
const uidInput   = document.getElementById('uidInput');
const loadingBox = document.getElementById('loadingBox');
const resultCard = document.getElementById('resultCard');
const uidError   = document.getElementById('uidError');

verifyBtn.addEventListener('click', doVerify);
uidInput.addEventListener('keydown', e => { if (e.key === 'Enter') doVerify(); });

async function doVerify() {
  const uid = uidInput.value.trim();
  uidError.textContent = '';
  resultCard.classList.add('hidden');

  if (!/^\d{5,15}$/.test(uid)) {
    uidError.textContent = 'Please enter a valid numeric UID.';
    return;
  }

  loadingBox.classList.remove('hidden');
  loadingBox.classList.add('fade-loop');

  try {
    const res  = await fetch('check.php?uid=' + encodeURIComponent(uid));
    const json = await res.json();
    await new Promise(r => setTimeout(r, 1400)); // smooth loading feel

    loadingBox.classList.add('hidden');

    if (!json.success || !json.data) {
      uidError.textContent = 'UID not found. Please check and try again.';
      return;
    }

    const d = json.data;
    document.getElementById('rName').textContent   = d.Name;
    document.getElementById('rUid').textContent    = d.UID;
    document.getElementById('rLevel').textContent  = d.Level;
    document.getElementById('rLikes').textContent  = d.Likes;
    document.getElementById('rRegion').textContent = d.Region;

    localStorage.setItem('ffAccount', JSON.stringify(d));

    resultCard.classList.remove('hidden');
    resultCard.classList.add('pop-in');
    celebrate();
  } catch (err) {
    loadingBox.classList.add('hidden');
    uidError.textContent = 'Server error. Please try again later.';
  }
}

document.getElementById('continueBtn').addEventListener('click', () => {
  document.body.classList.add('page-leave');
  setTimeout(() => location.href = 'shop.php', 350);
});

function celebrate() {
  const layer  = document.getElementById('confetti');
  const colors = ['#e01e2b','#ff5964','#ffd23f','#ffffff','#ff8fa3'];
  for (let i = 0; i < 80; i++) {
    const p = document.createElement('span');
    p.className = 'conf-piece';
    p.style.left = Math.random() * 100 + 'vw';
    p.style.background = colors[Math.floor(Math.random() * colors.length)];
    p.style.animationDelay = Math.random() * 0.5 + 's';
    layer.appendChild(p);
    setTimeout(() => p.remove(), 2600);
  }
}
</script>
</body>
</html>
