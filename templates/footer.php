<footer>
  <div class="footer-inner">
    <a href="<?= BASE_URL ?>/index.php" class="logo">
      <img src="<?= BASE_URL ?>/assets/img/logo-cyberescudo.jpg" alt="CyberEscudo Logo" class="nav-logo-img">
      <span>Cyber<span class="accent">Escudo</span></span>
    </a>
    <p class="footer-tagline"><?= e($t['footer']['tagline']) ?></p>
    <div class="footer-links">
      <span><?= e(SITE_EMAIL) ?></span>
      <span class="sep">·</span>
      <a href="<?= BASE_URL ?>/privacidad.php"><?= e($t['footer']['privacy']) ?></a>
    </div>
    <p class="footer-copy">© <?= SITE_YEAR ?> CyberEscudo — <?= e($t['footer']['built']) ?></p>
  </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>