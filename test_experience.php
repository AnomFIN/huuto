<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Huuto247 - Testi & Laadunvarmistus</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Test-specific styles */
        .test-section {
            margin: 3rem 0;
            padding: 2rem;
            border: 2px solid var(--gray-200);
            border-radius: 1rem;
            background: white;
        }
        
        .test-header {
            border-bottom: 1px solid var(--gray-200);
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .test-header h2 {
            color: var(--primary-600);
            margin-bottom: 0.5rem;
        }
        
        .test-checklist {
            display: grid;
            gap: 1rem;
            margin: 1.5rem 0;
        }
        
        .test-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 0.5rem;
        }
        
        .test-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid var(--primary-500);
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .test-checkbox.checked {
            background: var(--primary-500);
            color: white;
        }
        
        .quick-test-links {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .test-link {
            display: block;
            padding: 1rem;
            background: var(--primary-50);
            border: 2px solid var(--primary-200);
            border-radius: 0.5rem;
            text-decoration: none;
            color: var(--primary-700);
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .test-link:hover {
            background: var(--primary-100);
            border-color: var(--primary-300);
        }
        
        .performance-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .metric-card {
            padding: 1.5rem;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 0.5rem;
            text-align: center;
        }
        
        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--success-600);
            margin-bottom: 0.5rem;
        }
        
        .metric-label {
            font-size: 0.875rem;
            color: var(--gray-600);
        }
        
        @media (max-width: 768px) {
            .test-section {
                margin: 2rem 1rem;
                padding: 1rem;
            }
            
            .quick-test-links {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header" id="header">
        <div class="container header-content">
            <div class="header-brand">
                <div class="logo">
                    <span class="logo-text">Huuto247</span>
                </div>
            </div>
            
            <nav class="header-nav">
                <a href="/" class="nav-link">Etusivu</a>
                <a href="/test_experience.php" class="nav-link active">Testisivu</a>
                <a href="/auction.php?id=1" class="nav-link">Demo Huutokauppa</a>
                <a href="/category.php" class="nav-link">Kategoriat</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="hero-section">
            <h1 class="hero-title">Premium Huuto247 - Laadunvarmistus</h1>
            <p class="hero-subtitle">Testi kaikki premium-ominaisuudet ja varmista optimaalinen käyttökokemus</p>
        </div>

        <!-- Design System Test -->
        <section class="test-section">
            <div class="test-header">
                <h2>🎨 Design System & Visuaalinen Hierarkia</h2>
                <p>Testaa, että kaikki premium-komponentit näyttävät oikealta ja toimivat sujuvasti</p>
            </div>
            
            <div class="test-checklist">
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Premium-väripaletti ja typografia näkyy oikein kaikilla sivuilla</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Hover-efektit toimivat sujuvasti kaikissa komponenteissa</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Animaatiot ja siirtymät ovat luontevia ja nopeita</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Ikonigrafia on johdonmukaista läpi sivuston</span>
                </div>
            </div>
        </section>

        <!-- Category Navigation Test -->
        <section class="test-section">
            <div class="test-header">
                <h2>📁 Premium-kategorianavigointi</h2>
                <p>Varmista, että uusi kategorianavigointi toimii täydellisesti</p>
            </div>
            
            <div class="test-checklist">
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Kategoria-kortit ladataan oikein ikonien kanssa</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Klikkaaminen kategoriakortista aktivoi oikean kategorian</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Aktiivisen kategorian korostus näkyy selkeästi</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Quick Access -linkit johtavat oikeisiin kategoriasuodatuksiin</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Mobiilissa kategoriat skaalautuvat hyvin yksikolonnisiksi</span>
                </div>
            </div>
            
            <div class="quick-test-links">
                <a href="/" class="test-link">🏠 Testaa etusivu</a>
                <a href="/category.php?category=Ajoneuvot" class="test-link">🚗 Ajoneuvot</a>
                <a href="/category.php?category=Asunnot" class="test-link">🏘️ Asunnot</a>
                <a href="/category.php?featured=1" class="test-link">⭐ Suositut</a>
            </div>
        </section>

        <!-- Auction Detail Page Test -->
        <section class="test-section">
            <div class="test-header">
                <h2>🔨 Premium Huutokauppasivu</h2>
                <p>Testaa kaikki parannelted huutokauppasivun ominaisuudet</p>
            </div>
            
            <div class="test-checklist">
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Kuvagalleria toimii sujuvasti modal-näkymällä</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Countdown-ajastin näyttää ajan oikein ja päivittyy</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Tarjousformi toimii ja validoi syötteet</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Myyjäprofili ja luottamusindikaattorit näkyvät oikein</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Suosikit-toiminto toimii ja pysyy muistissa</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Responsiivinen layout toimii kaikilla laitteilla</span>
                </div>
            </div>
            
            <div class="quick-test-links">
                <a href="/auction.php?id=1" class="test-link">🔨 Demo huutokauppa</a>
                <a href="/auction.php?id=2" class="test-link">🏠 Asunto-huutokauppa</a>
                <a href="/auction.php?id=3" class="test-link">🚗 Ajoneuvo-huutokauppa</a>
            </div>
        </section>

        <!-- Performance & UX Test -->
        <section class="test-section">
            <div class="test-header">
                <h2>⚡ Suorituskyky & Käyttökokemus</h2>
                <p>Varmista, että sivusto latautuu nopeasti ja toimii sujuvasti</p>
            </div>
            
            <div class="performance-metrics">
                <div class="metric-card">
                    <div class="metric-value" id="loadTime">-</div>
                    <div class="metric-label">Latausaika (ms)</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value" id="domElements">-</div>
                    <div class="metric-label">DOM-elementtejä</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value" id="cssRules">-</div>
                    <div class="metric-label">CSS-sääntöjä</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value" id="javaScriptErrors">-</div>
                    <div class="metric-label">JS-virheitä</div>
                </div>
            </div>
            
            <div class="test-checklist">
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Sivut latautuvat alle 2 sekunnissa</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Ei JavaScript-virheitä konsolissa</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Kuvat latautuvat oikein tai näyttävät fallback-kuvan</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Lomakkeet toimivat ja validoivat syötteet</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Navigointi on intuitiivista ja nopeaa</span>
                </div>
            </div>
        </section>

        <!-- Cross-Browser Test -->
        <section class="test-section">
            <div class="test-header">
                <h2>🌐 Selainyhteensopivuus</h2>
                <p>Testaa, että sivusto toimii kaikissa moderneissa selaimissa</p>
            </div>
            
            <div class="test-checklist">
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Chrome: Layout ja toiminnallisuudet toimivat</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Firefox: CSS Grid ja Flexbox toimivat oikein</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Safari: Webkit-prefixit toimivat</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Edge: Modern CSS features toimivat</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Mobiiliselaimet: Touch-toiminnot ja viewport</span>
                </div>
            </div>
        </section>

        <!-- Security & Accessibility Test -->
        <section class="test-section">
            <div class="test-header">
                <h2>🔐 Turvallisuus & Saavutettavuus</h2>
                <p>Varmista, että sivusto on turvallinen ja kaikkien käytettävissä</p>
            </div>
            
            <div class="test-checklist">
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>HTTPS käytössä kaikilla sivuilla</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Lomakkeet validoivat syötteet myös server-puolella</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Alt-tekstit kaikilla kuvilla</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Navigointi toimii näppäimistöllä (Tab, Enter)</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Värikontrastit täyttävät WCAG-vaatimukset</span>
                </div>
                <div class="test-item">
                    <div class="test-checkbox" onclick="toggleCheck(this)"></div>
                    <span>Ruudunlukijat voivat navigoida sivustolla</span>
                </div>
            </div>
        </section>

        <!-- Test Results Summary -->
        <section class="test-section" style="background: var(--success-50); border-color: var(--success-200);">
            <div class="test-header">
                <h2>✅ Testien yhteenveto</h2>
                <p>Seurantaa varten: Kuvakaappaukset ja kysymykset</p>
            </div>
            
            <div style="display: grid; gap: 1rem; margin: 2rem 0;">
                <div style="padding: 1rem; background: white; border-radius: 0.5rem;">
                    <strong>📊 Testattu sivuja:</strong> <span id="testedPages">0</span>
                </div>
                <div style="padding: 1rem; background: white; border-radius: 0.5rem;">
                    <strong>✅ Läpäissyt testit:</strong> <span id="passedTests">0</span> / <span id="totalTests">0</span>
                </div>
                <div style="padding: 1rem; background: white; border-radius: 0.5rem;">
                    <strong>📝 Huomiot:</strong>
                    <textarea id="testNotes" style="width: 100%; min-height: 100px; margin-top: 0.5rem; padding: 0.5rem; border: 1px solid var(--gray-200); border-radius: 0.25rem;" placeholder="Kirjoita huomiot ja löydetyt ongelmat..."></textarea>
                </div>
            </div>
            
            <button onclick="generateTestReport()" style="padding: 1rem 2rem; background: var(--success-600); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">
                📄 Luo testiraportti
            </button>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-brand">
                <div class="logo">
                    <span class="logo-text">Huuto247</span>
                </div>
                <p>Premium-huutokauppakokemus uusilla standardeilla.</p>
            </div>
        </div>
    </footer>

    <script>
        // Test page functionality
        function toggleCheck(checkbox) {
            checkbox.classList.toggle('checked');
            if (checkbox.classList.contains('checked')) {
                checkbox.innerHTML = '✓';
            } else {
                checkbox.innerHTML = '';
            }
            updateTestStats();
        }

        function updateTestStats() {
            const total = document.querySelectorAll('.test-checkbox').length;
            const checked = document.querySelectorAll('.test-checkbox.checked').length;
            
            document.getElementById('totalTests').textContent = total;
            document.getElementById('passedTests').textContent = checked;
            
            // Update progress bar if you want to add one
            const progress = total > 0 ? (checked / total) * 100 : 0;
            console.log(`Test progress: ${progress.toFixed(1)}%`);
        }

        function generateTestReport() {
            const total = document.querySelectorAll('.test-checkbox').length;
            const checked = document.querySelectorAll('.test-checkbox.checked').length;
            const notes = document.getElementById('testNotes').value;
            
            const report = `
HUUTO247 PREMIUM - TESTIRAPORTTI
===============================
Testauspäivä: ${new Date().toLocaleDateString('fi-FI')}
Selain: ${navigator.userAgent}

TULOKSET:
- Testejä yhteensä: ${total}
- Läpäissi: ${checked}
- Onnistumisprosentti: ${total > 0 ? ((checked / total) * 100).toFixed(1) : 0}%

HUOMIOT:
${notes || 'Ei erityisiä huomioita.'}

SUORITUSKYKYMITTARIT:
- Latausaika: ${document.getElementById('loadTime').textContent} ms
- DOM-elementtejä: ${document.getElementById('domElements').textContent}
- CSS-sääntöjä: ${document.getElementById('cssRules').textContent}
- JavaScript-virheitä: ${document.getElementById('javaScriptErrors').textContent}
            `;
            
            const blob = new Blob([report], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `huuto247-testiraportti-${new Date().getTime()}.txt`;
            a.click();
            URL.revokeObjectURL(url);
        }

        // Collect performance metrics on page load
        window.addEventListener('load', () => {
            // Load time
            const navigationStart = performance.timing.navigationStart;
            const loadComplete = performance.timing.loadEventEnd;
            const loadTime = loadComplete - navigationStart;
            document.getElementById('loadTime').textContent = loadTime;
            
            // DOM elements
            const domElements = document.querySelectorAll('*').length;
            document.getElementById('domElements').textContent = domElements;
            
            // CSS rules (estimate)
            const cssRules = Array.from(document.styleSheets).reduce((count, sheet) => {
                try {
                    return count + (sheet.cssRules?.length || 0);
                } catch (e) {
                    return count;
                }
            }, 0);
            document.getElementById('cssRules').textContent = cssRules;
            
            // JavaScript errors (set up error tracking)
            let jsErrors = 0;
            window.addEventListener('error', () => jsErrors++);
            document.getElementById('javaScriptErrors').textContent = jsErrors;
            
            updateTestStats();
        });

        // Mobile viewport test
        function testViewport() {
            const viewport = window.innerWidth;
            console.log(`Viewport width: ${viewport}px`);
            
            if (viewport < 480) console.log('📱 Mobile viewport');
            else if (viewport < 768) console.log('📱 Large mobile viewport');
            else if (viewport < 1024) console.log('💻 Tablet viewport');
            else console.log('🖥️ Desktop viewport');
        }

        window.addEventListener('resize', testViewport);
        testViewport();
    </script>
</body>
</html>