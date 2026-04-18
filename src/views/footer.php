        </main>

    <!-- Premium Footer with Trust & Finnish Identity -->
    <footer class="premium-footer">
      <div class="footer-container">
        
        <!-- Trust Badges Section -->
        <div class="trust-section">
          <div class="trust-heading">
            <h3>🇫🇮 Luotettava suomalainen palvelu</h3>
            <p>Turvalliset maksut • Ostajan suoja • Yli 15 vuoden kokemus</p>
          </div>
          
          <div class="trust-badges">
            <!-- Finnish Quality -->
            <div class="trust-badge">
              <div class="badge-icon">🦢</div>
              <div class="badge-text">
                <span class="badge-title">Suomalainen</span>
                <span class="badge-desc">Lahen Huutokaupat Oy</span>
              </div>
            </div>
            
            <!-- Security -->
            <div class="trust-badge">
              <div class="badge-icon">🔒</div>
              <div class="badge-text">
                <span class="badge-title">Turvallinen</span>
                <span class="badge-desc">SSL-salattu</span>
              </div>
            </div>
            
            <!-- Users -->
            <div class="trust-badge">
              <div class="badge-icon">👥</div>
              <div class="badge-text">
                <span class="badge-title">89 000+</span>
                <span class="badge-desc">Tyytyväistä käyttäjää</span>
              </div>
            </div>
            
            <!-- Traffic -->
            <div class="trust-badge">
              <div class="badge-icon">📈</div>
              <div class="badge-text">
                <span class="badge-title">5M+ kävijää/kk</span>
                <span class="badge-desc">Suomen suurin</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Methods -->
        <div class="payment-section">
          <h4>💳 Turvallisia maksutapoja</h4>
          <div class="payment-methods">
            <div class="payment-group">
              <span class="payment-label">Verkkopankit:</span>
              <div class="bank-logos">
                <div class="bank-logo nordea">Nordea</div>
                <div class="bank-logo op">OP</div>
                <div class="bank-logo danske">Danske Bank</div>
                <div class="bank-logo handelsbanken">Handelsbanken</div>
                <div class="bank-logo spankki">S-Pankki</div>
              </div>
            </div>
            
            <div class="payment-group">
              <span class="payment-label">Kortit & Muu:</span>
              <div class="card-logos">
                <div class="payment-logo visa">VISA</div>
                <div class="payment-logo mastercard">Mastercard</div>
                <div class="payment-logo paypal">PayPal</div>
                <div class="payment-logo mobilepay">MobilePay</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Footer Content -->
        <div class="footer-main">
          <div class="footer-brand">
            <a href="index.php" class="footer-logo">
              <span class="logo-icon" aria-hidden="true">H</span>
              <span class="logo-text">uuto<span class="logo-247">24<span class="logo-7">/7</span></span></span>
            </a>
            <p class="brand-desc">Suomen johtava huutokauppapalvelu vuodesta 2009</p>
            <div class="quality-seals">
              <div class="seal finnish-seal">🇫🇮 100% Suomalainen</div>
              <div class="seal security-seal">🛡️ Ostajan suoja</div>
            </div>
          </div>

          <div class="footer-links">
            <div class="footer-col">
              <h4>🔨 Huutokaupat</h4>
              <button class="footer-category-link" data-action="open-category-menu">Kaikki kategoriat</button>
              <a href="/category.php?closing_soon=1">Päättyvät pian</a>
              <a href="/add_product.php">Myy kohteesi</a>
              <a href="/category.php?featured=1">Suositellut</a>
            </div>
            
            <div class="footer-col">
              <h4>🛠️ Tuki & Ohjeet</h4>
              <a href="/info.php?page=ohjeet">Käyttöohjeet</a>
              <a href="/info.php?page=tuki">Asiakastuki</a>
              <a href="/info.php?page=maksut">Maksutavat</a>
              <a href="/info.php?page=toimitus">Toimitus & Nouto</a>
            </div>
            
            <div class="footer-col">
              <h4>🏢 Yritys</h4>
              <a href="/info.php?page=meista">Tietoa meistä</a>
              <a href="/info.php?page=yhteystiedot">Yhteystiedot</a>
              <a href="/info.php?page=rekry">Työpaikat</a>
              <a href="/info.php?page=media">Medialle</a>
            </div>
            
            <div class="footer-col">
              <h4>⚖️ Laki & Turvallisuus</h4>
              <a href="/info.php?page=kayttoehdot">Käyttöehdot</a>
              <a href="/info.php?page=tietosuoja">Tietosuoja</a>
              <a href="/info.php?page=evasteet">Evästekäytäntö</a>
              <a href="/info.php?page=riidanratkaisu">Riidanratkaisu</a>
            </div>
          </div>
        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom">
          <div class="copyright">
            <p>&copy; 2026 <strong>Huuto247.fi</strong> - Lahen Huutokaupat Oy | Y-tunnus: 1234567-8</p>
            <p>Kaikki oikeudet pidätetään • <a href="/info.php?page=kayttoehdot">Käyttöehdot</a> • <a href="/info.php?page=tietosuoja">Tietosuoja</a></p>
          </div>
          
          <div class="footer-certifications">
            <div class="cert">🏅 Luotettava verkkokauppa</div>
            <div class="cert">✅ GDPR-yhteensopiva</div>
            <div class="cert">🔐 SSL-suojattu</div>
          </div>
        </div>
      </div>
    </footer>

        <script>
            function updateCountdowns() {
                document.querySelectorAll('.countdown[data-endtime], .countdown-time[data-endtime]').forEach((element) => {
                    const rawEndTime = String(element.dataset.endtime || '').trim();
                    if (!rawEndTime) {
                        return;
                    }

                    let endTimeMs = NaN;
                    if (/^\d+$/.test(rawEndTime)) {
                        const parsed = Number(rawEndTime);
                        endTimeMs = rawEndTime.length <= 10 ? parsed * 1000 : parsed;
                    } else {
                        let normalized = rawEndTime;
                        if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(normalized)) {
                            normalized = normalized.replace(' ', 'T') + 'Z';
                        }
                        endTimeMs = Date.parse(normalized);
                    }

                    if (!Number.isFinite(endTimeMs)) {
                        element.textContent = 'Päättynyt';
                        return;
                    }

                    const distance = endTimeMs - Date.now();
                    if (distance <= 0) {
                        element.textContent = 'Päättynyt';
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    if (days > 0) {
                        element.textContent = `${days}pv ${hours}h`;
                    } else if (hours > 0) {
                        element.textContent = `${hours}h ${minutes}min`;
                    } else {
                        element.textContent = `${minutes}min ${seconds}s`;
                    }
                });
            }

            setInterval(updateCountdowns, 1000);
            updateCountdowns();
        </script>
        <script src="/app.js" defer></script>

<!-- Category Mega Menu -->
<div class="category-mega-menu" id="categoryMegaMenu">
  <div class="category-mega-backdrop" id="categoryMegaBackdrop"></div>
  <div class="category-mega-panel">
    <div class="category-mega-header">
      <h2>📦 Kaikki kategoriat</h2>
      <button class="category-mega-close" id="categoryMegaClose" aria-label="Sulje">✕</button>
    </div>
    <div class="category-mega-search-wrap">
      <svg viewBox="0 0 24 24"><path d="M10.5 4a6.5 6.5 0 1 1 0 13 6.5 6.5 0 0 1 0-13Zm0 1.8a4.7 4.7 0 1 0 0 9.4 4.7 4.7 0 0 0 0-9.4Zm5.87 10.6 3.2 3.2-1.28 1.28-3.2-3.2 1.28-1.28Z"/></svg>
      <input type="text" class="category-mega-search" id="categoryMegaSearch" placeholder="Hae kategoriaa..." autocomplete="off">
    </div>
    <div class="category-mega-grid" id="categoryMegaGrid">
      <!-- Populated by JS -->
    </div>
  </div>
</div>

<script>
(function() {
  'use strict';

  // === FAVORITES SYSTEM (Cookie-based, works without login) ===
  function getCookieFavorites() {
    try {
      var stored = localStorage.getItem('huuto247-favorites');
      return stored ? JSON.parse(stored) : [];
    } catch(e) { return []; }
  }

  function updateFavCount() {
    var favs = getCookieFavorites();
    var countEls = document.querySelectorAll('.favorites-count');
    countEls.forEach(function(el) {
      el.textContent = favs.length > 0 ? favs.length : '';
      el.setAttribute('data-count', favs.length);
    });
  }

  // Favorites button opens category.php?tab=favorites
  var favBtn = document.getElementById('headerFavoritesBtn');
  if (favBtn) {
    favBtn.addEventListener('click', function() {
      window.location.href = '/category.php?tab=favorites';
    });
  }

  updateFavCount();
  // Listen for storage changes (cross-tab)
  window.addEventListener('storage', function(e) {
    if (e.key === 'huuto247-favorites') updateFavCount();
  });

  // === CATEGORY MEGA MENU ===
  var categoryIcons = {
    'Ajoneuvot': '🚗', 'Työkoneet': '🏗️', 'Asunnot': '🏠', 'Vapaa-aika': '⛵',
    'Piha': '🌿', 'Työkalut': '🔧', 'Rakennus': '🏗️', 'Sisustus': '🛋️',
    'Elektroniikka': '📱', 'Keräily': '🏆', 'Urheilu': '⚽', 'Vaatteet': '👕',
    'Taide': '🎨', 'Antiikki': '🏺', 'Kodin tavarat': '🏡', 'Musiikki': '🎵',
    'Kirjat': '📚', 'Pelit': '🎮', 'Muut': '📦'
  };

  var megaMenu = document.getElementById('categoryMegaMenu');
  var megaBackdrop = document.getElementById('categoryMegaBackdrop');
  var megaClose = document.getElementById('categoryMegaClose');
  var megaGrid = document.getElementById('categoryMegaGrid');
  var megaSearch = document.getElementById('categoryMegaSearch');
  var allCategoryItems = [];

  function openMegaMenu(e) {
    if (!megaMenu) return;
    if (e) e.preventDefault();
    
    // Load categories via AJAX if not loaded
    if (allCategoryItems.length === 0) {
      loadCategories();
    }
    megaMenu.classList.add('open');
    document.body.style.overflow = 'hidden';
    setTimeout(function() { 
      if (megaSearch) megaSearch.focus(); 
    }, 300);
  }

  function closeMegaMenu() {
    if (!megaMenu) return;
    megaMenu.classList.remove('open');
    document.body.style.overflow = '';
  }

  function loadCategories() {
    // Get categories from the header select dropdown
    var selectEl = document.querySelector('#headerSearchCategory, #searchCategory');
    if (selectEl) {
      var opts = selectEl.querySelectorAll('option');
      opts.forEach(function(opt) {
        var name = opt.textContent.trim();
        if (name && name !== 'Kaikki kategoriat') {
          allCategoryItems.push({ name: name, slug: opt.value || name });
        }
      });
    }
    renderCategories(allCategoryItems);
  }

  function renderCategories(cats) {
    if (!megaGrid) return;
    megaGrid.innerHTML = cats.map(function(cat, idx) {
      var icon = categoryIcons[cat.name] || '📦';
      return '<a href="/category.php?slug=' + encodeURIComponent(cat.slug) + '" class="category-mega-item" style="animation-delay:' + (idx * 30) + 'ms">' +
        '<span class="category-mega-icon">' + icon + '</span>' +
        '<div class="category-mega-info"><h3>' + cat.name + '</h3><span>Selaa kohteita →</span></div>' +
      '</a>';
    }).join('');
  }

  // Search filter
  if (megaSearch) {
    megaSearch.addEventListener('input', function() {
      var q = this.value.toLowerCase().trim();
      var filtered = allCategoryItems.filter(function(c) {
        return c.name.toLowerCase().indexOf(q) !== -1;
      });
      renderCategories(filtered);
    });
  }

  // Global event delegation for category menu triggers
  document.addEventListener('click', function(e) {
    // Check if clicked element or any parent has the category menu trigger
    var trigger = e.target.closest('[data-action=\"open-category-menu\"]');
    if (trigger) {
      e.preventDefault();
      openMegaMenu(e);
      return;
    }
  });

  // Close triggers
  if (megaBackdrop) megaBackdrop.addEventListener('click', closeMegaMenu);
  if (megaClose) megaClose.addEventListener('click', closeMegaMenu);
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && megaMenu && megaMenu.classList.contains('open')) {
      closeMegaMenu();
    }
  });

})();
</script>
</body>
</html>
