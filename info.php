<?php
require_once __DIR__ . '/bootstrap.php';

$page = isset($_GET['page']) ? trim((string)$_GET['page']) : '';
$allowedPages = [
    'myyminen', 'ohjeet', 'tuki', 'maksut', 'meista', 'yhteystiedot', 'rekry',
    'kayttoehdot', 'tietosuoja', 'evasteet', 'tietoa-palvelusta', 'tietoa-huutajalle',
    'myyntiehdot', 'hinnasto', 'maksutavat', 'asiakaspalvelu', 'uutiskirje',
    'blogi', 'kampanjat', 'tietoa-meista', 'lahen-huutokauppa', 'meille-toihin',
    'medialle', 'tietosuojaseloste', 'lapinakyvyys', 'saavutettavuus',
];

if (!in_array($page, $allowedPages, true)) {
    $page = 'ohjeet';
}

$pageTitles = [
    'myyminen' => 'Aloita myyminen',
    'ohjeet' => 'Ohjeet ja vinkit',
    'tuki' => 'Asiakastuki',
    'maksut' => 'Maksutavat',
    'meista' => 'Tietoa meistä',
    'yhteystiedot' => 'Yhteystiedot',
    'rekry' => 'Työpaikat',
    'kayttoehdot' => 'Käyttöehdot',
    'tietosuoja' => 'Tietosuoja',
    'evasteet' => 'Evästeet',
];

$pageTitle = ($pageTitles[$page] ?? ucfirst($page)) . ' - ' . SITE_NAME;
include SRC_PATH . '/views/header.php';

$contentFile = __DIR__ . '/content/' . $page . '.html';
?>

<div class="info-page">
    <div class="info-container">
        <?php if ($page === 'myyminen'): ?>
            <div class="info-header-banner" style="margin-bottom: 2rem;">
                <a href="/add_product.php" class="btn-ilmoita-kohde">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>
                    Ilmoita kohde
                </a>
            </div>

            <article class="info-content-card">
                <h1>Aloita myyminen</h1>
                <p class="info-lead">Hyvä ilmoitus lisää kiinnostusta ja vähentää epäselvyyksiä huutokaupan aikana.</p>

                <h2>Ilmoituksen sisältö</h2>
                <ul>
                    <li>Kirjoita selkeä otsikko ja tarkka tuotekuvaus.</li>
                    <li>Kerro näkyvät viat, puutteet, käyttöhistoria ja toimitussisältö.</li>
                    <li>Lisää useita laadukkaita kuvia eri kulmista.</li>
                    <li>Määritä realistinen aloitushinta, varahinta ja mahdollinen osta heti -hinta.</li>
                </ul>

                <h2>Ilmoittajan velvollisuudet</h2>
                <ul>
                    <li>Kohdetta ei saa myydä muuta kautta huutokaupan ollessa käynnissä.</li>
                    <li>Ilmoitus tulee täyttää mahdollisimman tarkasti ja rehellisesti, mukaan lukien viat ja haitat.</li>
                    <li>Ilmoittajan tulee olla tavoitettavissa puhelimitse ja/tai sähköpostitse huutokaupan ajan.</li>
                    <li>Tarjouksia ei saa ottaa vastaan huutokaupan ohi; kaikki tarjoukset ohjataan Huuto247-palveluun.</li>
                    <li>Ilmoittajan velvollisuus on estää lähipiirin näennäiset huudot ilman todellista ostoaikomusta.</li>
                    <li>Ilmoittaja ei saa huutaa omiin kohteisiinsa.</li>
                </ul>

                <h2>Myyjän vastuut</h2>
                <ul>
                    <li>Myyjä vastaa ilmoituksen oikeellisuudesta ja siitä, että kohde vastaa annettuja tietoja olennaisilta osin.</li>
                    <li>Jos myyjä on pidättänyt oikeuden hyväksyä tai hylätä korkeimman tarjouksen, päätös on ilmoitettava kahden (2) arkipäivän sisällä huutokaupan päättymisestä.</li>
                    <li>Jos kohde myydään muodossa "Myydään eniten tarjoavalle", myyjä sitoutuu luovuttamaan kohteen korkeimman tarjouksen tehneelle.</li>
                </ul>
            </article>

        <?php elseif (is_file($contentFile)): ?>
            <article class="info-content-card">
                <?php include $contentFile; ?>
            </article>
        <?php else: ?>
            <article class="info-content-card">
                <h1><?php echo htmlspecialchars($pageTitles[$page] ?? ucfirst($page), ENT_QUOTES, 'UTF-8'); ?></h1>
                <p>Tämän sivun sisältö on tulossa pian.</p>
            </article>
        <?php endif; ?>
    </div>
</div>

<style>
.info-page {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 clamp(16px, 3vw, 32px);
}

.info-header-banner {
    text-align: center;
    padding: 2rem 0;
}

.info-content-card {
    background: var(--surface);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    padding: clamp(24px, 4vw, 48px);
    box-shadow: var(--shadow-premium);
}

.info-content-card h1 {
    font-size: clamp(1.6rem, 3vw, 2.2rem);
    margin-top: 0;
    -webkit-text-fill-color: initial;
    background: none;
    color: var(--text-900);
}

.info-content-card h2 {
    font-size: 1.15rem;
    margin-top: 2rem;
    -webkit-text-fill-color: initial;
    background: none;
    color: var(--text-900);
    padding-bottom: 8px;
    border-bottom: 2px solid var(--line-soft);
}

.info-lead {
    color: var(--text-700);
    font-size: 1.05rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.info-content-card ul {
    padding-left: 1.5rem;
    margin: 1rem 0;
}

.info-content-card li {
    margin-bottom: 0.6rem;
    color: var(--text-700);
    line-height: 1.6;
}

.info-content-card li::marker {
    color: var(--accent-600);
}
</style>

<?php include SRC_PATH . '/views/footer.php'; ?>
