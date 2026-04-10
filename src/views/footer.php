        </main>

    <footer class="site-footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-brand">
            <a href="index.php" class="footer-logo">
              <span class="logo-mark" aria-hidden="true"></span>
              <span>HUUTO247<span class="logo-dot">.fi</span></span>
            </a>
            <p>Suomen johtava huutokauppapalvelu</p>
          </div>

          <div class="footer-links">
            <div class="footer-col">
              <h4>Huutokaupat</h4>
              <a href="/category.php">Kaikki kategoriat</a>
              <a href="/category.php?closing_soon=1">Päättyvät pian</a>
              <a href="/add_product.php">Myy kohteesi</a>
            </div>
            
            <div class="footer-col">
              <h4>Tuki</h4>
              <a href="/info.php?page=ohjeet">Ohjeet</a>
              <a href="/info.php?page=tuki">Asiakastuki</a>
              <a href="/info.php?page=maksut">Maksutavat</a>
            </div>
            
            <div class="footer-col">
              <h4>Yritys</h4>
              <a href="/info.php?page=meista">Tietoa meistä</a>
              <a href="/info.php?page=yhteystiedot">Yhteystiedot</a>
              <a href="/info.php?page=rekry">Työpaikat</a>
            </div>
          </div>

          <div class="footer-meta">
            <div class="footer-trust">
              <span>Suomalainen palvelu</span>
              <span>5M+ vierailua/kk</span>
              <span>Yli 89 000 käyttäjää</span>
            </div>
            
            <div class="footer-legal">
              <a href="/info.php?page=kayttoehdot">Käyttöehdot</a>
              <a href="/info.php?page=tietosuoja">Tietosuoja</a>
              <a href="/info.php?page=evasteet">Evästeet</a>
            </div>
          </div>
        </div>

        <div class="footer-bottom">
          <p>&copy; 2026 Huuto247.fi - Kaikki oikeudet pidätetään</p>
          <p>Lahen Huutokaupat Oy</p>
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
</body>
</html>
