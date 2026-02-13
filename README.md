# Huuto - Suomalainen Huutokauppa-alusta

Moderni verkkohuutokauppa-alusta rakennettu PHP:llä ja MySQL:llä. Sisältää täyden huutokauppatoiminnallisuuden mukaan lukien tarjoukset, kategoriat ja tuotelistat.

## Ominaisuudet

- 🏠 **Kategoriat**: 8 pääkategoriaa (Kiinteistöt, Ajoneuvot, Elektroniikka, jne.)
- 💰 **Huutokauppatoiminnot**: Reaaliaikaiset tarjoukset, lähtöhinta, osta heti -hinnat
- ⏱️ **Countdown-ajastimet**: Näyttää jäljellä olevan ajan
- 📱 **Responsiivinen**: Toimii kaikilla laitteilla (Tailwind CSS)
- 🔍 **Haku**: Etsi kohteita otsikon tai kuvauksen perusteella
- 📊 **Tilastot**: Tarjousmäärät, katselukerrat, seuraajat
- 🖼️ **Kuvagalleria**: Useat kuvat per kohde

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
│   └── category.php        # Kategorianäkymä
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
