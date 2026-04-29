/* CyberEscudo — Main JS */
(function () {
  'use strict';

  // ── Navbar scroll class ────────────────────────────────────────────────────
  var navbar = document.getElementById('navbar');
  if (navbar) {
    function onScroll() {
      navbar.classList.toggle('scrolled', window.scrollY > 40);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ── Mobile menu toggle ─────────────────────────────────────────────────────
  var burger = document.getElementById('burger');
  var mobileMenu = document.getElementById('mobile-menu');
  if (burger && mobileMenu) {
    burger.addEventListener('click', function () {
      mobileMenu.classList.toggle('open');
    });
    // Close when clicking a link
    mobileMenu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        mobileMenu.classList.remove('open');
      });
    });
  }

  // ── Smooth scroll for hero CTA ─────────────────────────────────────────────
  document.querySelectorAll('[data-scroll-to]').forEach(function (el) {
    el.addEventListener('click', function () {
      var target = document.getElementById(el.dataset.scrollTo);
      if (target) target.scrollIntoView({ behavior: 'smooth' });
    });
  });

  // ── Copy-to-clipboard for code blocks ─────────────────────────────────────
  document.querySelectorAll('.prose pre').forEach(function (pre) {
    var code = pre.querySelector('code');
    if (!code) return;

    // Wrap pre in a relative container so the button sits OUTSIDE the
    // overflow-x:auto scroll area and is never clipped
    var wrapper = document.createElement('div');
    wrapper.className = 'code-wrapper';
    pre.parentNode.insertBefore(wrapper, pre);
    wrapper.appendChild(pre);

    var btn = document.createElement('button');
    btn.className = 'copy-btn';
    btn.setAttribute('aria-label', 'Copiar código');
    btn.innerHTML =
      '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        '<rect x="9" y="9" width="13" height="13" rx="2"/>' +
        '<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>' +
      '</svg>' +
      '<span>Copiar</span>';

    wrapper.appendChild(btn);

    btn.addEventListener('click', function () {
      var text = code.innerText;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () {
          showCopied(btn);
        }).catch(function () { fallbackCopy(text, btn); });
      } else {
        fallbackCopy(text, btn);
      }
    });
  });

  function showCopied(btn) {
    btn.classList.add('copied');
    btn.querySelector('span').textContent = '¡Copiado!';
    setTimeout(function () {
      btn.classList.remove('copied');
      btn.querySelector('span').textContent = 'Copiar';
    }, 2000);
  }

  function fallbackCopy(text, btn) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.cssText = 'position:fixed;top:-9999px;left:-9999px;opacity:0';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); showCopied(btn); } catch (e) {}
    document.body.removeChild(ta);
  }

  // ── Reading progress bar ───────────────────────────────────────────────────
  var readingBar = document.getElementById('reading-bar');
  if (readingBar) {
    function updateBar() {
      var total = document.documentElement.scrollHeight - window.innerHeight;
      readingBar.style.width = (total > 0 ? Math.min(window.scrollY / total * 100, 100) : 0) + '%';
    }
    window.addEventListener('scroll', updateBar, { passive: true });
    updateBar();
  }

  // ── Read time estimation ───────────────────────────────────────────────────
  var proseEl = document.querySelector('.prose');
  var readTimeMeta = document.getElementById('read-time-meta');
  if (proseEl && readTimeMeta) {
    var words = proseEl.innerText.trim().split(/\s+/).length;
    var mins  = Math.max(1, Math.round(words / 200));
    var isEs  = document.documentElement.lang === 'es';
    readTimeMeta.textContent = isEs ? mins + ' min de lectura' : mins + ' min read';
  }

  // ── Table of contents ──────────────────────────────────────────────────────
  var tocAnchor = document.getElementById('toc-anchor');
  if (proseEl && tocAnchor) {
    var headings = Array.prototype.slice.call(proseEl.querySelectorAll('h2'));
    if (headings.length > 2) {
      var isEs2 = document.documentElement.lang === 'es';
      var toc   = document.createElement('div');
      toc.id    = 'toc-container';
      toc.innerHTML = '<h4>' + (isEs2 ? 'Contenido' : 'Contents') + '</h4><ul id="toc-list"></ul>';
      var ul = toc.querySelector('ul');
      headings.forEach(function (h, i) {
        if (!h.id) h.id = 'sec-' + i;
        var li = document.createElement('li');
        li.innerHTML = '<span class="toc-arrow">›</span><a href="#' + h.id + '">' + h.textContent + '</a>';
        ul.appendChild(li);
      });
      tocAnchor.appendChild(toc);

      // Highlight active section while scrolling
      if ('IntersectionObserver' in window) {
        var tocLinks = ul.querySelectorAll('a');
        var activeLink = null;
        var secObserver = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            var link = ul.querySelector('a[href="#' + entry.target.id + '"]');
            if (!link) return;
            if (entry.isIntersecting) {
              if (activeLink) activeLink.classList.remove('toc-active');
              activeLink = link;
              link.classList.add('toc-active');
            }
          });
        }, { rootMargin: '-10% 0px -60% 0px' });
        headings.forEach(function (h) { secObserver.observe(h); });
      }
    }
  }

  // ── Project filter + search (index page only) ──────────────────────────────
  var projSearch  = document.getElementById('proj-search');
  var catFilters  = document.getElementById('cat-filters');
  var diffFilters = document.getElementById('diff-filters');
  var noResults   = document.getElementById('no-results');
  if (projSearch && catFilters) {
    var activeCat  = '';
    var activeDiff = '';

    function applyFilters() {
      var q = projSearch.value.toLowerCase().trim();
      var cards   = document.querySelectorAll('.projects-grid .card');
      var visible = 0;
      cards.forEach(function (card) {
        var catOk    = !activeCat  || card.dataset.cat  === activeCat;
        var diffOk   = !activeDiff || card.dataset.diff === activeDiff;
        var searchOk = !q          || (card.dataset.search || '').indexOf(q) !== -1;
        var show = catOk && diffOk && searchOk;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
      });
      if (noResults) noResults.classList.toggle('visible', visible === 0);
    }

    projSearch.addEventListener('input', applyFilters);

    catFilters.querySelectorAll('.filter-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        catFilters.querySelectorAll('.filter-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        activeCat = btn.dataset.cat;
        applyFilters();
      });
    });

    diffFilters.querySelectorAll('.filter-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        diffFilters.querySelectorAll('.filter-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        activeDiff = btn.dataset.diff;
        applyFilters();
      });
    });
  }

  // ── Intersection observer: fade-in sections ────────────────────────────────
  if ('IntersectionObserver' in window) {
    var style = document.createElement('style');
    style.textContent = '.fade-in{opacity:0;transform:translateY(24px);transition:opacity .55s ease,transform .55s ease}.fade-in.visible{opacity:1;transform:none}';
    document.head.appendChild(style);

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.card, .manual-card, .profile-card, .contact-card, .donate-card').forEach(function (el) {
      el.classList.add('fade-in');
      observer.observe(el);
    });
  }
})();
