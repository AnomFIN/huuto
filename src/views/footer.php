        </main>

        <footer class="site-footer">
            <div class="container footer-grid">
                <div>
                    <div class="footer-brand"><span class="logo-mark" aria-hidden="true"></span>Huuto247.fi</div>
                    <p>Täysin suomalainen palvelu, jonka tuottaa Lahen Huutokaupat Oy.</p>
                    <p>Yli viisi miljoonaa vierailua kuukaudessa.</p>
                    <div class="socials">
                        <a href="/info.php?page=some">Youtube</a>
                        <a href="/info.php?page=some">Instagram</a>
                        <a href="/info.php?page=some">Facebook</a>
                    </div>
                </div>
                <div class="links-col">
                    <a href="/info.php?page=tietoa-palvelusta">Tietoa palvelusta</a>
                    <a href="/info.php?page=tietoa-huutajalle">Tietoa huutajalle</a>
                    <a href="/info.php?page=kayttoehdot">Palvelun käyttöehdot</a>
                    <a href="/info.php?page=myyminen">Aloita myyminen</a>
                    <a href="/info.php?page=myyjana-huuto247">Mitä tarkoittaa myyjänä Huuto247?</a>
                    <a href="/info.php?page=kayttajien-valinen-huutokauppa">Mitä tarkoittaa käyttäjien välinen huutokauppa?</a>
                    <a href="/info.php?page=myyntiehdot">Huuto247-myyntiehdot</a>
                    <a href="/info.php?page=hinnasto">Hinnasto</a>
                    <a href="/info.php?page=maksutavat">Maksutavat</a>
                    <a href="/info.php?page=asiakaspalvelu">Asiakaspalvelu</a>
                    <a href="/info.php?page=ohjeet">Ohjeet ja vinkit</a>
                    <a href="/info.php?page=uutiskirje">Tilaa uutiskirje</a>
                    <a href="/info.php?page=blogi">Blogi</a>
                    <a href="/info.php?page=kampanjat">Kampanjat</a>
                    <a href="/info.php?page=tietoa-meista">Tietoa meistä</a>
                    <a href="/info.php?page=lahen-huutokauppa">Lahen huutokauppa</a>
                    <a href="/info.php?page=meille-toihin">Meille töihin</a>
                    <a href="/info.php?page=medialle">Medialle</a>
                    <a href="/info.php?page=tietosuojaseloste">Tietosuojaseloste</a>
                    <a href="/info.php?page=evasteet">Evästeasetukset</a>
                    <a href="/info.php?page=lapinakyvyys">Läpinäkyvyysraportointi</a>
                    <a href="/info.php?page=saavutettavuus">Saavutettavuusseloste</a>
                </div>
            </div>
            <div class="container footer-bottom">
                <small>© 2026 Huuto247.fi</small>
                <a class="cookie-pill" href="/info.php?page=evasteet">Evästeasetukset</a>
            </div>
            <div class="container" style="padding-top:.45rem; padding-bottom:.2rem; color:#d3deea; font-size:.78rem; opacity:.92;">
                Lahen Huutokaupat Oy · Y-tunnus 3480428-5 · PRH/YTJ rekisteritiedot · Toimitusjohtaja Samu Petteri Kuitunen · puh. 0408179806 · info@huuto247.fi
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
</body>
</html>
