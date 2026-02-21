<?php
require_once __DIR__ . '/bootstrap.php';

$pageKey = trim((string)($_GET['page'] ?? 'tietoa-palvelusta'));

$pages = [
  'tietoa-palvelusta' => [
    'title' => 'Tietoa palvelusta',
    'lead' => 'Huuto247 (huuto247.fi) on suomalainen huutokauppapalvelu, jossa yritykset ja yksityiset ostajat kohtaavat läpinäkyvän tarjousprosessin kautta.',
    'sections' => [
      ['Miten palvelu toimii', '<ul><li>Myyjä julkaisee kohteen, lisää kuvat ja määrittää huutokaupan ehdot.</li><li>Ostajat tekevät tarjouksia huutokaupan aikana ja näkevät olennaiset tiedot kohteesta.</li><li>Huutokaupan päättyessä korkein hyväksytty tarjous voittaa, ellei kohteessa ole ehtoja (esim. varahinta).</li><li>Maksu, nouto ja toimitus tehdään kohdekohtaisilla ehdoilla.</li></ul>'],
      ['Perusperiaatteet', '<p>Palvelussa korostuvat selkeä ilmoitussisältö, todelliset huutotarkoitukset, maksuehtojen noudattaminen sekä myyjän ja ostajan välinen asiallinen viestintä. Tietoja voidaan tarkentaa huutokaupan aikana, mutta olennaisia ehtoja ei saa muuttaa harhaanjohtavasti.</p>'],
      ['Huuto247 ja huuto247.fi', '<p>Palvelunimi <strong>Huuto247</strong> ja verkkotunnus <strong>huuto247.fi</strong> tarkoittavat tässä yhteydessä samaa palvelua. Kun ehdoissa tai ohjeissa viitataan Huuto247-palveluun, viittaus koskee huuto247.fi-verkkopalvelua ja sen tarjoamaa huutokauppa-alustaa.</p>'],
      ['Vastuunjako', '<p>Huuto247 toimii markkinapaikkana ja ilmoitusalustana. Kohteen kunnosta, ominaisuuksista, toimituksesta ja sopimuksen täyttämisestä vastaavat ensisijaisesti kaupan osapuolet. Palvelu voi puuttua sääntöjen vastaiseen toimintaan, poistaa sisältöä tai rajoittaa käyttöoikeutta ehtojen mukaisesti.</p>'],
    ],
  ],
  'tietoa-huutajalle' => [
    'title' => 'Tietoa huutajalle',
    'lead' => 'Ennen huutoa kannattaa lukea kohdekuvaus, kuvat, toimitusehdot, maksutapa ja myyjän erityisehdot huolellisesti.',
    'sections' => [
      ['Ennen huutoa', '<ul><li>Varmista tuotteen kunto, mitat, mahdolliset puutteet ja palautusehdot.</li><li>Tarkista noutopaikka, toimituskulut, pakkauskulut ja mahdolliset säilytysmaksut.</li><li>Arvioi kokonaiskustannus: huutohinta + toimitus + mahdolliset lisäkulut.</li><li>Huutaminen on sitova toimenpide, ellei kohdekohtaisissa ehdoissa toisin mainita.</li></ul>'],
      ['Huutajan velvollisuudet huutokaupan aikana', '<ul><li>Palvelua käytetään omalla vastuulla ja kustannuksella, hyvän tavan mukaisesti.</li><li>Huutajan tulee olla täysi-ikäinen.</li><li>Tee huudot vain omalla käyttäjätunnuksella; käyttäjätunnus on henkilökohtainen.</li><li>Tarjousten tekeminen ilman ostoaikomusta on kiellettyä.</li><li>Huutokauppaan osallistuminen palvelusta estetyn käyttäjän puolesta on ehdottomasti kiellettyä.</li><li>Pidä yhteystietosi (sähköposti, puhelin ja osoite) ajan tasalla.</li></ul>'],
      ['Voiton jälkeen', '<ul><li>Korkein tarjous sitoo huutajaa, ellei kohdekohtaisissa ehdoissa toisin todeta.</li><li>Voitettu kohde tulee maksaa viimeistään yhden (1) arkipäivän kuluessa huutokaupan päättymisestä tai tarjouksen hyväksymisestä.</li><li>Maksamatta jättäminen ei ole asianmukainen peruutusilmoitus.</li><li>Mikäli kuluttajansuojalain mukainen peruuttamisoikeus soveltuu, ilmoitus tehdään asiakaspalveluun erikseen.</li><li>Toistuvat lunastamattomat kohteet voivat johtaa käyttöoikeuden rajoittamiseen tai sulkemiseen.</li></ul>'],
      ['Yhteenveto huutajalle', '<p>Palvelun käyttö on sujuvaa, kun tutustut kohteeseen ennen huutoa, maksat voittamasi kohteet ajallaan, sovit noudosta nopeasti maksun jälkeen ja toimit asiallisesti muita käyttäjiä kohtaan.</p>'],
    ],
  ],
  'kayttoehdot' => [
    'title' => 'Palvelun käyttöehdot',
    'lead' => 'Nämä käyttöehdot määrittävät Huuto247.fi-palvelun käytön keskeiset säännöt.',
    'sections' => [
      ['1. Käyttöoikeus', '<p>Palvelua saa käyttää lain, hyvän tavan ja näiden ehtojen mukaisesti. Käyttäjä vastaa tunnuksensa käytöstä ja tietoturvasta.</p>'],
      ['2. Kielletty toiminta', '<ul><li>Harhaanjohtavat ilmoitukset, tekaistut huudot ja markkinan manipulointi.</li><li>Toisen henkilön tietojen tai tunnusten luvaton käyttö.</li><li>Sisältö, joka rikkoo lakia, immateriaalioikeuksia tai hyvää tapaa.</li></ul>'],
      ['3. Sopimussuhde', '<p>Huuto247.fi toimii markkinapaikkana. Kauppasopimus syntyy pääosin ostajan ja myyjän välille huutokaupan päätyttyä kohdekohtaisten ehtojen perusteella.</p>'],
      ['4. Vastuunrajoitus', '<p>Palvelu pyritään pitämään käytettävissä ilman keskeytyksiä, mutta teknisistä häiriöistä voi aiheutua katkoksia. Huuto247.fi ei vastaa välillisistä vahingoista pakottavan lain sallimissa rajoissa.</p>'],
      ['5. Valvonta ja seuraamukset', '<p>Palvelu voi varoittaa, rajoittaa käyttöä, poistaa sisältöä tai sulkea käyttäjätilin ehtorikkomuksen perusteella. Vakavat rikkomukset voidaan raportoida viranomaisille.</p>'],
    ],
  ],
  'myyminen' => [
    'title' => 'Aloita myyminen',
    'lead' => 'Hyvä ilmoitus lisää kiinnostusta ja vähentää epäselvyyksiä huutokaupan aikana.',
    'sections' => [
      ['Ilmoituksen sisältö', '<ul><li>Kirjoita selkeä otsikko ja tarkka tuotekuvaus.</li><li>Kerro näkyvät viat, puutteet, käyttöhistoria ja toimitussisältö.</li><li>Lisää useita laadukkaita kuvia eri kulmista.</li><li>Määritä realistinen aloitushinta, varahinta ja mahdollinen osta heti -hinta.</li></ul>'],
      ['Ilmoittajan velvollisuudet', '<ul><li>Kohdetta ei saa myydä muuta kautta huutokaupan ollessa käynnissä.</li><li>Ilmoitus tulee täyttää mahdollisimman tarkasti ja rehellisesti, mukaan lukien viat ja haitat.</li><li>Ilmoittajan tulee olla tavoitettavissa puhelimitse ja/tai sähköpostitse huutokaupan ajan.</li><li>Tarjouksia ei saa ottaa vastaan huutokaupan ohi; kaikki tarjoukset ohjataan Huuto247-palveluun.</li><li>Ilmoittajan velvollisuus on estää lähipiirin näennäiset huudot ilman todellista ostoaikomusta.</li><li>Ilmoittaja ei saa huutaa omiin kohteisiinsa.</li></ul>'],
      ['Myyjän vastuut', '<ul><li>Myyjä vastaa ilmoituksen oikeellisuudesta ja siitä, että kohde vastaa annettuja tietoja olennaisilta osin.</li><li>Jos myyjä on pidättänyt oikeuden hyväksyä tai hylätä korkeimman tarjouksen, päätös on ilmoitettava kahden (2) arkipäivän sisällä huutokaupan päättymisestä.</li><li>Jos kohde myydään muodossa “Myydään eniten tarjoavalle”, myyjä sitoutuu luovuttamaan kohteen korkeimman tarjouksen tehneelle.</li><li>Mahdollinen hintavaraus voidaan ilmoittaa etukäteen; jos hintavaraus täyttyy, kohde muuttuu “Myydään eniten tarjoavalle” -tilaan.</li><li>Piileviin virheisiin, vastuisiin ja kuluttajansuojaan sovelletaan voimassa olevaa lainsäädäntöä.</li></ul>'],
      ['Toimitus ja nouto', '<p>Ilmoita toimitustapa, käsittelyaika, noutoajat, mahdolliset varastointimaksut ja kuljetusvastuu yksiselitteisesti. Jos toimitus ei sisälly hintaan, kerro kulurakenne etukäteen.</p>'],
    ],
  ],
  'myyntiehdot' => [
    'title' => 'Myyntiehdot',
    'lead' => 'Myyntiehdoissa määritetään huutokauppakaupan keskeiset toimitus-, vastuu- ja maksukäytännöt.',
    'sections' => [
      ['Kaupan syntyminen', '<p>Kauppa syntyy, kun huutokauppa päättyy ja kohde myydään korkeimmalle hyväksytylle tarjoajalle. Myyjä voi käyttää varahintaa, jos se on ilmoitettu kohteessa.</p>'],
      ['Kohteen kunto ja tarkastus', '<p>Kohde myydään lähtökohtaisesti siinä kunnossa kuin se on ilmoitushetkellä. Ostajan tulee tutustua saatavilla oleviin tietoihin ennen huutoa. Myyjä ei saa salata olennaisia virheitä.</p>'],
      ['Maksuvelvollisuus', '<ul><li>Ostajan tulee maksaa kohde ilmoitetussa määräajassa.</li><li>Viivästynyt maksu voi aiheuttaa viivästyskuluja ja käyttörajoituksia.</li><li>Maksamatta jättäminen voidaan käsitellä sopimusrikkomuksena.</li></ul>'],
      ['Nouto ja varastointi', '<p>Nouto- ja varastointiehdot ilmoitetaan kohdekohtaisesti. Mikäli nouto viivästyy, myyjä voi periä kohtuullisen varastointimaksun ehtojen mukaisesti.</p>'],
      ['Käyttäjien väliset kaupat', '<p>Käyttäjien välisessä kaupassa Huuto247 toimii ilmoitusalustana, ja kauppa toteutuu suoraan ilmoittajan ja huutajan välillä. Näihin kauppoihin sovelletaan käyttäjien välisten kauppojen ehtoja, kohdekohtaista ilmoitusta sekä kulloinkin voimassa olevia käyttöehtoja.</p>'],
      ['Kauppatavan korjaaminen', '<p>Jos luvanvarainen tai muutoin käyttäjien väliseksi kuuluva kohde on julkaistu virheellisellä kauppatavalla, huutokauppa tulee perua ja kohde julkaista uudelleen oikeilla ehdoilla. Kauppatapaa ei voi muuttaa kesken käynnissä olevan huutokaupan.</p>'],
    ],
  ],
  'kayttajien-valinen-huutokauppa' => [
    'title' => 'Mitä tarkoittaa käyttäjien välinen huutokauppa?',
    'lead' => 'Käyttäjien välisessä huutokaupassa Huuto247 (huuto247.fi) toimii ilmoitusalustana, ja kauppa tehdään suoraan myyjän ja ostajan välillä.',
    'sections' => [
      ['Tiivistelmä kauppatavasta', '<ul><li>Kauppa toteutetaan suoraan ilmoittajan ja huutajan välillä.</li><li>Ostaja maksaa kohteen ilmoittajan antamien maksutietojen mukaan huutokaupan päätyttyä.</li><li>Ilmoittaja kuittaa kohteen maksetuksi palvelussa, kun maksu on vastaanotettu.</li><li>Ilmoittaja vastaa kaupan dokumenteista ja tarvittaessa rekisteröintiasiakirjoista.</li><li>Ilmoittaja (ja mahdollinen toimeksiantaja) vastaa kohteen kunnosta ja kaupasta soveltuvan lainsäädännön mukaisesti.</li></ul>'],
      ['Milloin kohde on käyttäjien välinen', '<p>Osa kohteista määräytyy automaattisesti käyttäjien välisiksi kategoriansa tai luonteensa perusteella.</p><ul><li>Ulosoton kohteet</li><li>Konkurssihuutokaupat</li><li>Asunnot, kiinteistöt ja tontit</li><li>Antiikki ja taide</li><li>Kohteet, joiden lähtöhinta on yli 50 000 €</li><li>Luvanvaraiset kohteet (esim. lämpöpumput, pelastusajoneuvot)</li><li>Osakkeet ja arvopaperit</li><li>Luonnonvaraisten riistaeläinten tarkastamaton liha</li><li>Domain-osoitteet</li><li>Oikeus sähköisen palvelun käyttöön (esim. verkkokoulutus)</li><li>Uusi tuote, joka myydään yksityishenkilön toimeksiantona</li></ul>'],
      ['Luvanvaraisuuskysymys ja julkaisu', '<p>Jos ilmoituslomake kysyy “Vaatiiko tuotteen myyminen, ostaminen tai asentaminen luvan?” ja vastaat kyllä, kohde julkaistaan käyttäjien välisenä. Jos tiedät kohteen kuuluvan käyttäjien väliseksi, mutta et näe kysymystä tai et ole varma, ota yhteys asiakaspalveluun ennen julkaisua.</p>'],
      ['Ehdot ja kielletyt tuotteet', '<p>Jokaiseen huutokauppaan sovelletaan kohdekohtaisia myyntiehtoja, käyttäjien välisten kauppojen ehtoja sekä Huuto247-palvelun käyttöehtoja. Tarkista lisäksi palvelussa kokonaan kielletyt tuotteet ennen ilmoituksen julkaisua.</p>'],
    ],
  ],
  'myyjana-huuto247' => [
    'title' => 'Mitä tarkoittaa myyjänä Huuto247?',
    'lead' => 'Kun kohteen myyjänä on Huuto247, Huuto247 (huuto247.fi) toimii kaupan osapuolena ja vastaa kaupankäynnin toteutuksesta ehtojen mukaisesti.',
    'sections' => [
      ['Tiivistelmä kaupasta', '<ul><li>Huuto247 toimii ostajan suuntaan kohteen myyjänä.</li><li>Rahaliikenne kulkee Huuto247in kautta kohdekohtaisten ehtojen mukaisesti.</li><li>Kaupan dokumentit (esim. kauppakirjat ja kuitit) hoidetaan palvelun prosessin mukaisesti.</li><li>Ajoneuvojen osalta rekisteröintiasiat hoidetaan ilmoitettujen ehtojen ja soveltuvan sääntelyn mukaan.</li></ul>'],
      ['Miten tunnistat Huuto247-kaupan', '<p>Kun ilmoituksessa kerrotaan, että kohteen myyjänä toimii Huuto247, kyseessä on palvelun kautta hallinnoitu kauppatapa. Ilmoittaja voi olla yritys tai yksityishenkilö, mutta myyjän rooli ostajan suuntaan määräytyy ilmoituksessa kuvatun kauppatavan perusteella.</p>'],
      ['Sovellettavat ehdot', '<p>Huuto247-kauppoihin sovelletaan kohdekohtaisia myyntiehtoja, palvelun käyttöehtoja sekä muuta kulloinkin voimassa olevaa lainsäädäntöä. Epäselvissä tilanteissa ota yhteys asiakaspalveluun ennen maksua tai luovutusta.</p>'],
    ],
  ],
  'hinnasto' => [
    'title' => 'Hinnasto',
    'lead' => 'Palvelun kulut voivat koostua huutohinnasta, toimituskuluista ja mahdollisista lisäpalvelumaksuista.',
    'sections' => [
      ['Perushinnoittelu', '<ul><li>Huutohinta määräytyy tarjouskilpailun perusteella.</li><li>Toimitus- ja pakkauskulut ilmoitetaan kohdekohtaisesti.</li><li>Lisämaksut (esim. varastointi) perustuvat ilmoitettuihin ehtoihin.</li></ul>'],
      ['Yritysmyynti', '<p>Yritysmyynnissä hinnoitteluun voi sisältyä arvonlisäveroa, käsittelykuluja tai logistisia palvelumaksuja. Ehtojen tulee näkyä kohteen tiedoissa.</p>'],
      ['Laskutus ja kuittaus', '<p>Maksutapahtumista muodostetaan tosite käytettävän maksukanavan mukaisesti. Mahdolliset reklamaatiot tulee tehdä kohtuullisessa ajassa ehtojen mukaan.</p>'],
    ],
  ],
  'maksutavat' => [
    'title' => 'Maksutavat',
    'lead' => 'Maksutapa määräytyy kohdekohtaisesti ja voi sisältää verkkomaksun, tilisiirron tai muun erikseen ilmoitetun maksutavan.',
    'sections' => [
      ['Hyväksytyt maksutavat', '<ul><li>Tilisiirto / verkkopankkimaksu</li><li>Korttimaksu tai maksupalveluntarjoajan kautta toteutettava maksu</li><li>Kohdekohtainen laskutus yritysmyynnissä</li></ul>'],
      ['Maksun kohdistaminen', '<p>Viitenumero tai muu yksilöintitieto tulee ilmoittaa maksussa, jotta suoritus kohdistuu oikeaan kohteeseen. Väärät maksutiedot voivat viivästyttää toimitusta.</p>'],
      ['Maksuturva', '<p>Suosittelemme käyttämään vain palvelussa tai kohde-ehdoissa hyväksyttyjä maksutapoja. Älä lähetä maksua epävirallisiin kanaviin ilman varmistusta.</p>'],
    ],
  ],
  'asiakaspalvelu' => [
    'title' => 'Asiakaspalvelu',
    'lead' => 'Asiakaspalvelu auttaa huutokauppaan, käyttäjätiliin, ilmoituksiin ja reklamaatioihin liittyvissä asioissa.',
    'sections' => [
      ['Yhteystiedot', '<p><strong>Lahen Huutokaupat Oy</strong><br>Sähköposti: info@huuto247.fi<br>Lisäosoite: samu.kuitunen@huuto247.fi<br>Puhelin: 0408179806 / +35840179806<br>Postiosoite: Pursimiehenkatu 2 A 20, 15140 Lahti</p>'],
      ['Reklamaatiot', '<p>Reklamaatio tulee tehdä kirjallisesti ja yksilöidä kohde, tapahtuma-aika, virheen kuvaus sekä mahdollinen vaatimus. Liitä mukaan kuvat tai muu näyttö, jos mahdollista.</p>'],
      ['Yritystiedot viranomaisrekistereissä', '<p>Y-tunnus 3480428-5. Tiedot perustuvat käyttäjän toimittamiin virallisiin tietoihin (lähdeviittaus: YTJ/PRH käyttäjän antaman aineiston perusteella).</p>'],
    ],
  ],
  'ohjeet' => [
    'title' => 'Ohjeet ja vinkit',
    'lead' => 'Näillä käytännön vinkeillä saat huutokaupoista enemmän hyötyä sekä myyjänä että ostajana.',
    'sections' => [
      ['Ostajalle', '<ul><li>Vertaa vastaavia kohteita ennen huutoa.</li><li>Tee enimmäisbudjetti ja pidä siitä kiinni.</li><li>Tarkista toimituskulut aina ennen viimeistä huutoa.</li></ul>'],
      ['Myyjälle', '<ul><li>Kuvaa kohde hyvässä valossa useasta kulmasta.</li><li>Kerro virheet avoimesti – se vähentää riitoja.</li><li>Aseta realistinen aloitushinta, joka houkuttelee tarjouksia.</li></ul>'],
      ['Turvallisuus', '<ul><li>Pidä viestintä ensisijaisesti palvelun kautta.</li><li>Älä luovuta tunnuksia tai kertakäyttökoodeja.</li><li>Raportoi epäilyttävä toiminta asiakaspalveluun.</li></ul>'],
    ],
  ],
  'uutiskirje' => [
    'title' => 'Tilaa uutiskirje',
    'lead' => 'Uutiskirje kokoaa yhteen ajankohtaiset huutokaupat, kampanjat ja palvelupäivitykset.',
    'sections' => [
      ['Tilausehdot', '<p>Uutiskirjeen tilaaminen on vapaaehtoista. Voit perua tilauksen milloin tahansa uutiskirjeen linkistä tai asiakaspalvelun kautta.</p>'],
      ['Sisältö', '<ul><li>Uudet ja kiinnostavat huutokauppakohteet</li><li>Palvelun käyttövinkit</li><li>Tarjoukset ja ajankohtaiset tiedotteet</li></ul>'],
    ],
  ],
  'blogi' => [
    'title' => 'Blogi',
    'lead' => 'Blogissa julkaistaan huutokauppaan liittyviä ajankohtaisia artikkeleita ja käytännön oppaita.',
    'sections' => [
      ['Sisältöperiaatteet', '<p>Blogisisältö on informatiivista eikä muodosta sitovaa sopimusta. Mahdolliset ehdot ja hinnat määräytyvät aina kohdekohtaisissa tiedoissa.</p>'],
      ['Aihealueet', '<ul><li>Huutokauppavinkit ja markkinakatsaukset</li><li>Yritysmyynnin käytännöt</li><li>Toimitus- ja maksuprosessien kehittäminen</li></ul>'],
    ],
  ],
  'kampanjat' => [
    'title' => 'Kampanjat',
    'lead' => 'Kampanjoissa voi olla määräaikaisia etuja, jotka koskevat valittuja kohteita tai palveluita.',
    'sections' => [
      ['Kampanjaehdot', '<p>Kampanjaedut ovat voimassa rajatun ajan ja niiden soveltaminen voi edellyttää erillistä kampanjakoodia tai ehtojen täyttymistä.</p>'],
      ['Rajoitukset', '<ul><li>Kampanjaa ei voi aina yhdistää muihin etuihin.</li><li>Palvelu pidättää oikeuden korjata ilmeiset hintavirheet.</li><li>Ehtoja voidaan päivittää kampanjan aikana perustellusta syystä.</li></ul>'],
    ],
  ],
  'tietoa-meista' => [
    'title' => 'Tietoa meistä',
    'lead' => 'Huuto247.fi toimii Lahen Huutokaupat Oy:n aputoiminimen alla ja keskittyy huutokauppaliiketoimintaan Suomessa.',
    'sections' => [
      ['Yritysesittely', '<p><strong>Virallinen nimi:</strong> Lahen Huutokaupat Oy<br><strong>Yhtiömuoto:</strong> Osakeyhtiö<br><strong>Perustettu:</strong> 3.10.2024<br><strong>Toimiala:</strong> Huutokaupat<br><strong>Aputoiminimet:</strong> www.huuto247.fi, HUUTO247, HUUTO247.FI</p>'],
      ['Talousluvut (2024)', '<p>Liikevaihto 1 931 000 €, liikevaihdon muutos +62,0 %, liiketoiminnan voitto 98 000 €, liikevoittoprosentti 5,3 %, omavaraisuusaste 70 %, henkilöstömäärä 4.</p>'],
      ['Vastuuhenkilöt', '<p><strong>Toimitusjohtaja:</strong> Samu Petteri Kuitunen (10/2024 alkaen)<br><strong>Toimitusjohtajan sijainen:</strong> Juha Petteri Marttila (10/2024 alkaen)</p>'],
    ],
  ],
  'lahen-huutokauppa' => [
    'title' => 'Lahen Huutokauppa',
    'lead' => 'Lahen Huutokaupat Oy toimii huutokauppojen järjestäjänä sekä markkinapaikan ylläpitäjänä.',
    'sections' => [
      ['Päätoimipaikka', '<p>Pursimiehenkatu 2 A 20, 15140 Lahti</p>'],
      ['Palvelut', '<ul><li>Yritysten ja erämyyntien huutokaupat</li><li>Ajoneuvojen, koneiden ja kulutustavaroiden huutokaupat</li><li>Verkkohuutokauppapalvelu osoitteessa www.huuto247.fi</li></ul>'],
    ],
  ],
  'meille-toihin' => [
    'title' => 'Meille töihin',
    'lead' => 'Etsimme kasvavaan huutokauppaliiketoimintaan kaupallista, teknistä ja asiakaspalveluosaamista.',
    'sections' => [
      ['Kiinnostuitko?', '<p>Lähetä avoin hakemus sekä CV sähköpostiin info@huuto247.fi. Kerro hakemuksessa osaamisalueesi, toivottu rooli ja mahdollinen aloitusaikataulu.</p>'],
      ['Tietosuoja rekrytoinnissa', '<p>Käsittelemme hakemustietoja luottamuksellisesti rekrytointitarkoituksessa ja säilytämme niitä vain tarpeellisen ajan.</p>'],
    ],
  ],
  'medialle' => [
    'title' => 'Medialle',
    'lead' => 'Median yhteydenotot, haastattelupyynnöt ja materiaalitoiveet käsitellään keskitetysti.',
    'sections' => [
      ['Yhteystiedot medialle', '<p>Sähköposti: info@huuto247.fi<br>Lisäosoite: samu.kuitunen@huuto247.fi</p>'],
      ['Aineiston käyttö', '<p>Yrityksen nimi- ja yhteystietoja saa käyttää uutisoinnissa asiallisessa yhteydessä. Kuvamateriaalin käyttö edellyttää tapauskohtaista lupaa.</p>'],
    ],
  ],
  'tietosuojaseloste' => [
    'title' => 'Tietosuojaseloste',
    'lead' => 'Tässä selosteessa kuvataan, miten henkilötietoja kerätään, käytetään, säilytetään ja suojataan palvelussa.',
    'sections' => [
      ['Rekisterinpitäjä', '<p>Lahen Huutokaupat Oy, Y-tunnus 3480428-5, Pursimiehenkatu 2 A 20, 15140 Lahti, info@huuto247.fi.</p>'],
      ['Käsiteltävät tiedot', '<ul><li>Käyttäjätilin tiedot (nimi, sähköposti, puhelin, käyttäjätunnus)</li><li>Transaktiotiedot (huudot, ostot, myynnit, viestit)</li><li>Tekniset tiedot (IP-osoite, lokitiedot, istuntotiedot)</li></ul>'],
      ['Käsittelyn perusteet', '<p>Henkilötietoja käsitellään sopimuksen täyttämiseksi, lakisääteisten velvoitteiden noudattamiseksi, oikeutetun edun perusteella sekä tarvittaessa suostumuksella.</p>'],
      ['Säilytysajat', '<p>Tietoja säilytetään niin kauan kuin asiakassuhde, lainsäädäntö tai oikeutettu etu sitä edellyttää. Vanhentuneet tiedot poistetaan tai anonymisoidaan säännöllisesti.</p>'],
      ['Rekisteröidyn oikeudet', '<p>Sinulla on oikeus tarkastaa, oikaista, poistaa, rajoittaa käsittelyä ja vastustaa käsittelyä soveltuvan lain mukaisesti. Pyynnöt: info@huuto247.fi.</p>'],
    ],
  ],
  'evasteet' => [
    'title' => 'Evästeasetukset',
    'lead' => 'Evästeet parantavat käyttökokemusta, mahdollistavat istunnon hallinnan ja auttavat palvelun kehittämisessä.',
    'sections' => [
      ['Evästeluokat', '<ul><li>Välttämättömät evästeet (kirjautuminen, tietoturva)</li><li>Toiminnalliset evästeet (käyttöasetukset)</li><li>Analytiikkaevästeet (palvelun kehittäminen)</li></ul>'],
      ['Suostumuksen hallinta', '<p>Voit muuttaa evästevalintoja selaimen asetuksista ja palvelun evästetyökalun kautta. Välttämättömiä evästeitä ei voi poistaa käytöstä ilman, että palvelun ydintoiminnot heikentyvät.</p>'],
    ],
  ],
  'lapinakyvyys' => [
    'title' => 'Läpinäkyvyysraportointi',
    'lead' => 'Julkaisemme tiivistettyä tietoa palvelun valvonnasta, sisältötoimenpiteistä ja käyttörajoituksista.',
    'sections' => [
      ['Raportin sisältö', '<ul><li>Poistetun tai rajoitetun sisällön määrät</li><li>Tilirajoitukset ja valitusprosessit</li><li>Yhteistyö viranomaisten kanssa lain edellyttämissä tilanteissa</li></ul>'],
      ['Periaate', '<p>Raportointi toteutetaan niin, ettei yksittäisten käyttäjien yksityisyys vaarannu. Julkaisemme vain tarpeellisen ja anonymisoidun tason tietoa.</p>'],
    ],
  ],
  'saavutettavuus' => [
    'title' => 'Saavutettavuusseloste',
    'lead' => 'Tavoitteena on, että palvelu toimii mahdollisimman hyvin eri laitteilla, avustavilla teknologioilla ja erilaisissa käyttötilanteissa.',
    'sections' => [
      ['Nykytila', '<p>Parannamme jatkuvasti näppäimistökäytettävyyttä, kontrasteja, lomakeohjeita ja semanttista rakennetta. Havaitut puutteet priorisoidaan kehitykseen.</p>'],
      ['Palaute', '<p>Voit ilmoittaa saavutettavuuspuutteista osoitteeseen info@huuto247.fi. Käsittelemme palautteet ilman aiheetonta viivytystä.</p>'],
    ],
  ],
  'some' => [
    'title' => 'Sosiaalinen media',
    'lead' => 'Seuraa Huuto247.fi:tä sosiaalisen median kanavissa.',
    'sections' => [
      ['Kanavat', '<p>Ajankohtaiset sisällöt, kampanjat ja vinkit julkaistaan palvelun virallisissa somekanavissa.</p>'],
    ],
  ],
];

if (!isset($pages[$pageKey])) {
    http_response_code(404);
    $pageKey = 'tietoa-palvelusta';
}

$page = $pages[$pageKey];
$title = (string)$page['title'];
$lead = (string)$page['lead'];
$pageTitle = $title . ' - ' . SITE_NAME;

include SRC_PATH . '/views/header.php';
?>

<style>
  .info-wrap { max-width: 900px; margin: 0 auto; }
  .info-card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); box-shadow: var(--shadow-1); padding: 1.2rem 1.25rem; }
  .info-card h1 { margin: 0 0 .55rem; color: var(--text-900); font-size: clamp(1.35rem, 2vw, 1.9rem); }
  .info-card p { color: var(--text-700); line-height: 1.62; }
  .info-section { margin-top: 1.2rem; padding-top: 1.1rem; border-top: 1px solid var(--line); }
  .info-section h2 { margin: 0 0 .55rem; font-size: 1.08rem; }
  .info-section ul { margin: .35rem 0 .15rem; padding-left: 1.2rem; color: var(--text-700); }
  .info-section li { margin: .25rem 0; line-height: 1.55; }
  .info-note { margin-top: 1rem; padding: .65rem .75rem; border: 1px solid var(--line); border-radius: 10px; background: var(--surface-soft); color: var(--text-700); font-size: .88rem; }
  .info-meta { margin-top: .9rem; font-size: .85rem; color: var(--text-700); }
</style>

<section class="info-wrap">
  <article class="info-card">
    <h1><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <p><?php echo htmlspecialchars($lead, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php foreach (($page['sections'] ?? []) as $section): ?>
      <section class="info-section">
        <h2><?php echo htmlspecialchars((string)$section[0], ENT_QUOTES, 'UTF-8'); ?></h2>
        <div><?php echo (string)$section[1]; ?></div>
      </section>
    <?php endforeach; ?>

    <div class="info-note">
      Tämä infosivu sisältää yleistasoista ohje- ja ehtosisältöä huutokauppakäyttöön. Kohdekohtaiset ehdot voivat täydentää näitä tietoja.
    </div>

    <div class="info-meta">Sivuavain: <?php echo htmlspecialchars($pageKey, ENT_QUOTES, 'UTF-8'); ?></div>
  </article>
</section>

<?php include SRC_PATH . '/views/footer.php'; ?>
