<?php
require 'config.php';
bump_stat('visits');
$settings = get_data('settings');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>FF Diamond TopUp — Fast Checkout</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ================= HEADER ================= -->
<header class="site-header">
  <div class="hdr-inner">
    <div class="hdr-left"><img src="assets/ffmax_icon.png" alt="FF Max" class="hdr-ico"></div>
    <div class="hdr-center"><img src="assets/bisicon.png" alt="BIS" class="hdr-ico-lg"></div>
    <div class="hdr-right"></div>
  </div>
</header>

<main class="wrap">

  <!-- ============ BANNER ============ -->
  <section class="banner-box">
    <img src="assets/banner.png" alt="Banner" class="banner-img">
  </section>

  <!-- ============ FEATURE TAGS ============ -->
  <section class="tags-row">
    <div class="tag">
      <svg viewBox="0 0 24 24" class="tag-svg"><path d="M13 2L3 14h7l-1 8 11-14h-7z"/></svg>
      Fast Checkout
    </div>
    <div class="tag">
      <svg viewBox="0 0 24 24" class="tag-svg"><path d="M20 8h-3V4H3v13h2a3 3 0 006 0h4a3 3 0 006 0h1v-6l-2-3zM8 18.5A1.5 1.5 0 116.5 17 1.5 1.5 0 018 18.5zm10 0A1.5 1.5 0 1116.5 17 1.5 1.5 0 0118 18.5z"/></svg>
      Instant Delivery
    </div>
  </section>

  <!-- ============ FF LOGO ============ -->
  <div class="fflogo-wrap"><img src="assets/fflogo.png" alt="FF Logo" class="fflogo"></div>

  <!-- ============ VERIFY UID ============ -->
  <section class="verify-card">
    <h2 class="sec-title">Verify Your UID</h2>
    <div class="uid-input-row">
      <input type="text" id="uidInput" inputmode="numeric" placeholder="Enter your Free Fire UID" autocomplete="off">
      <button id="verifyBtn" class="btn-red">Verify</button>
    </div>
    <p class="hint">Enter the numeric UID shown in your game profile.</p>

    <!-- Loading -->
    <div id="loadBox" class="load-box hide">
      <img src="assets/fficon.png" class="load-ico" alt="Loading">
      <p class="load-txt">Verifying your account…</p>
    </div>

    <!-- Result card -->
    <div id="resultBox" class="result-card hide">
      <div class="avatar-wrap"><img src="assets/avatar.png" class="avatar" alt="Avatar"></div>
      <h3 id="rName" class="r-name"></h3>
      <div class="r-grid">
        <div class="r-item"><span class="r-lbl">UID</span><span id="rUid" class="r-val"></span></div>
        <div class="r-item"><span class="r-lbl">Level</span><span id="rLevel" class="r-val"></span></div>
        <div class="r-item"><span class="r-lbl">Likes</span><span id="rLikes" class="r-val"></span></div>
        <div class="r-item"><span class="r-lbl">Region</span><span id="rRegion" class="r-val"></span></div>
      </div>
      <button id="continueBtn" class="btn-red full">Continue Shopping</button>
    </div>

    <p id="errBox" class="err hide"></p>
  </section>

</main>

<!-- ============ FOOTER ============ -->
<footer class="site-footer">
  <img src="assets/garenalogo.png" alt="Garena" class="foot-logo">
  <p class="foot-txt">This is an independent top-up store. Not officially affiliated with Garena.</p>
  <p class="foot-copy">© <?php echo date('Y'); ?> FF Diamond TopUp. All rights reserved.</p>
</footer>

<div id="confetti" class="confetti-layer"></div>

<script src="app.js"></script>
</body>
</html>