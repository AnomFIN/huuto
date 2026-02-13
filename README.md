# Huuto - Suomalainen Huutokauppa-alusta

Moderni verkkohuutokauppa-alustan prototyyppi rakennettu PHP:llä ja MySQL:llä. Sisältää peruslistaukset ja kategoriat, sekä alustavan arkkitehtuurin huutokauppa-, haku- ja tarjouslogiikalle.

## Ominaisuudet

- 🏠 **Kategoriat**: 8 pääkategoriaa (Kiinteistöt, Ajoneuvot, Elektroniikka, jne.)
- 💰 **Huutokauppatoiminnot (backend)**: Lähtöhinta ja osta heti -hinnat mallinnettu tietokantaan ja PHP-luokkiin. Täysi reaaliaikainen tarjous-UI ja erilliset `/search.php`, `/auctions.php`, `/categories.php`-reitit eivät vielä ole käytössä.
- ⏱️ **Countdown-ajastimet**: Näyttää jäljellä olevan ajan kohteen näkymässä
- 📱 **Responsiivinen**: Toimii kaikilla laitteilla (Tailwind CSS)
- 🔍 **Haku (suunniteltu)**: Hakuotsikko ja hakulogiikka ovat osittain toteutettuina, mutta erillinen haku-sivu ja -reitit eivät vielä ole valmiit.
- 📊 **Tilastot (osittain toteutettu)**: Peruslukujen näyttäminen (esim. tarjousmäärä) on tuettu datatasolla, mutta laajemmat tilastot ja raportointi eivät ole vielä mukana.
- 🖼️ **Kuvagalleria (suunniteltu)**: Tuki useille kuville per kohde on suunnitteilla; nykyinen käyttöliittymä saattaa näyttää vain yhden kuvan.
- 🤖 **AI-tuotekuvausgeneraattori (kokeellinen)**: AI-pohjainen tuotekuvausten generointi on konseptoitu, mutta ei oletuksena käytössä valmiina käyttöliittymäominaisuutena.
- ➕ **Helppo tuotteen lisäys**: Ei vaadi kirjautumista, täydellinen testaukseen

## Teknologiat

- **Backend**: PHP 8.0+
- **Tietokanta**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: Tailwind CSS (CDN)
- **Arkkitehtuuri**: MVC-pattern

## Asennus

### Vaatimukset

- PHP 8.0 tai uudempi
- MySQL 5.7 tai uudempi (tai MariaDB 10.3+)
- Web-palvelin (Apache, Nginx, tai PHP:n sisäänrakennettu palvelin)

### Asennusohjeet

1. **Kloonaa repositorio**
   ```bash
   git clone https://github.com/AnomFIN/huuto.git
   cd huuto
   ```

2. **Luo tietokanta**
   ```bash
   mysql -u root -p < database/schema.sql
   ```

3. **Lisää esimerkkidata (valinnainen)**
   ```bash
   mysql -u root -p < database/sample_data.sql
   ```

4. **Konfiguroi tietokantayhteys**
   
   Voit käyttää ympäristömuuttujia tai muokata `config/database.php` tiedostoa:
   ```bash
   export DB_HOST=localhost
   export DB_NAME=huuto_db
   export DB_USER=root
   export DB_PASS=your_password
   ```

5. **Käynnistä kehityspalvelin**
   ```bash
   cd public
   php -S localhost:8000
   ```

6. **Avaa selaimessa**
   
   Siirry osoitteeseen: http://localhost:8000

## Projektin rakenne

```
huuto/
├── config/
│   ├── config.php          # Yleiset asetukset
│   └── database.php        # Tietokanta-asetukset
├── database/
│   ├── schema.sql          # Tietokantarakenne
│   └── sample_data.sql     # Esimerkkidata
├── public/
│   ├── index.php           # Etusivu
│   ├── auction.php         # Yksittäinen huutokauppa
│   ├── category.php        # Kategorianäkymä
│   ├── add_product.php     # Tuotteen lisäys (tietokannalla)
│   ├── add_product_demo.html # Tuotteen lisäys (demo, ei DB)
│   ├── demo.html           # Demo etusivu
│   └── demo-auction.html   # Demo huutokauppa
├── src/
│   ├── models/
│   │   ├── Database.php    # Tietokantayhteys
│   │   ├── Auction.php     # Huutokauppa-malli
│   │   └── Category.php    # Kategoria-malli
│   └── views/
│       ├── header.php      # Ylätunniste
│       └── footer.php      # Alatunniste
└── uploads/                # Ladatut kuvat
```

## Käyttö

### Etusivu
- Näyttää päättyvät huutokaupat
- Kategoriat selattavissa
- Haku-toiminto

### Tuotesivu
- Tuotteen kuvat ja kuvaus
- Nykyinen hinta ja tarjoushistoria
- Countdown-ajastin
- Myyjän tiedot
- Tilastot (tarjoukset, katselukerrat)

### Kategoriat
- Selaa kohteita kategorian mukaan
- 8 pääkategoriaa:
  - Kiinteistöt
  - Ajoneuvot
  - Elektroniikka
  - Kodin tavarat
  - Urheilu
  - Vaatteet
  - Keräily
  - Muut

### Tuotteen lisäys (Add Product)
- **Ei vaadi kirjautumista** - Täydellinen testaukseen
- **AI-kuvausgeneraattori** - Lataa kuvia ja AI luo automaattisesti otsikon ja kuvauksen
- **Älykäs hinnoittelu** - Ehdottaa hintoja kategorian perusteella
- **Kuvien esikatselu** - Näe ladatut kuvat ennen tallentamista
- **Täysi lomake** - Kaikki huutokaupan kentät yhdellä sivulla
- Käytä: `add_product_demo.html` (demo) tai `add_product.php` (täysi versio)

## Tietokanta

Tietokanta sisältää seuraavat taulut:

- `users` - Käyttäjätiedot
- `categories` - Kategoriat
- `auctions` - Huutokaupat
- `auction_images` - Tuotekuvat
- `bids` - Tarjoukset
- `watchlist` - Seuratut kohteet

## Turvallisuus

- Prepared statements (SQL injection -suojaus)
- Password hashing (bcrypt)
- XSS-suojaus (htmlspecialchars)
- Session security
- Input validation

## Kehitys

### Tulevat ominaisuudet
- [ ] Käyttäjien rekisteröinti ja kirjautuminen
- [ ] Automaattinen tarjous (proxy bidding)
- [ ] Sähköposti-ilmoitukset
- [ ] Maksuintegraatio
- [ ] Admin-paneeli
- [ ] API

## Lisenssi

Tämä projekti on kehitetty opetustarkoituksiin.

## Tekijä

AnomFIN - 2026
