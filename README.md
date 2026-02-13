# Huuto - Suomalainen Huutokauppa-alusta

Moderni PHP-pohjainen huutokauppa-alusta suomalaisille käyttäjille.

## 📋 Sisältö

- [Ominaisuudet](#ominaisuudet)
- [Tekniset vaatimukset](#tekniset-vaatimukset)
- [Asennus](#asennus)
- [Käyttöönotto](#käyttöönotto)
- [Projektirakenteen](#projektirakenne)
- [Testikäyttäjät](#testikäyttäjät)
- [Ominaisuudet](#ominaisuudet-yksityiskohtaisesti)

## ✨ Ominaisuudet

### Julkiset ominaisuudet
- 🏠 Etusivu päättyvät pian & uusimmat ilmoitukset
- 🔍 Hakutoiminto (hakusana, kategoria, päättyvät pian)
- 📂 21 kategoriaa ikoneilla
- 🏷️ Ilmoitussivut kuvineen ja tietoineen
- ⏰ Reaaliaikaiset ajastimet
- 📱 Responsiivinen mobile-first -muotoilu
- 🍪 Evästebanneri ja -asetukset
- 📄 15+ staattista infosivua
- 📰 Blogi-toiminto

### Käyttäjätoiminnot
- 📝 Rekisteröityminen (sähköpostivahvistus mock)
- 🔐 Kirjautuminen rate limitillä
- 👤 Käyttäjäprofiili
- ➕ Ilmoituksen luonti (max 8 kuvaa)
- 💰 Huutaminen validoinnilla
- 🏆 Omat huudot ja voitot
- 📊 Omat ilmoitukset

### Admin-toiminnot
- 🔧 Admin-paneeli tilastoilla
- 👥 Käyttäjien hallinta (estä/aktivoi)
- 📝 Ilmoitusten hallinta (arkistoi/poista)
- 📂 Kategorioiden hallinta
- 📰 Blogin hallinta

### Turvallisuus
- 🔒 Salasanat: `password_hash` / `password_verify`
- 🛡️ CSRF-suojaus kaikissa lomakkeissa
- 💉 SQL injection -suojaus (prepared statements)
- ⏱️ Rate limiting kirjautumisessa
- 🔐 Roolipohjainen pääsynhallinta

### SEO & Suorituskyky
- 🔗 Siistit SEO-ystävälliset URLit
- 🖼️ Kuvien lazy loading
- 📱 Mobile-first responsive design
- ⚡ Kevyt ja nopea

## 🔧 Tekniset vaatimukset

- **PHP**: 8.0 tai uudempi
- **Tietokanta**: MySQL 5.7+ tai MariaDB 10.2+
- **Webpalvelin**: Apache (mod_rewrite) tai Nginx
- **PHP-laajennukset**: PDO, PDO_MySQL

## 📦 Asennus

### 1. Kloonaa repositorio

```bash
git clone https://github.com/AnomFIN/huuto.git
cd huuto
```

### 2. Aseta webpalvelin

#### Apache

Varmista että `mod_rewrite` on käytössä:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Aseta DocumentRoot osoittamaan `/path/to/huuto/public` -kansioon.

Esimerkki VirtualHost-konfiguraatio:

```apache
<VirtualHost *:80>
    ServerName huuto.local
    DocumentRoot /var/www/huuto/public
    
    <Directory /var/www/huuto/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/huuto-error.log
    CustomLog ${APACHE_LOG_DIR}/huuto-access.log combined
</VirtualHost>
```

#### Nginx

Esimerkki Nginx-konfiguraatio:

```nginx
server {
    listen 80;
    server_name huuto.local;
    root /var/www/huuto/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 3. Luo tietokanta

```bash
mysql -u root -p
```

```sql
CREATE DATABASE huuto CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'huuto_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON huuto.* TO 'huuto_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Suorita asennusohjelma

Avaa selaimella: `http://yourdomain.com/setup.php`

Asennusohjelma:
1. Kysyy tietokannan yhteystiedot
2. Luo tarvittavat taulut automaattisesti
3. Luo admin-käyttäjän
4. Luo 21 kategoriaa
5. Luo 63+ seed-ilmoitusta (3 per kategoria)
6. Luo 15 staattista sivua
7. Tallentaa asetukset `config/config.php` -tiedostoon

**Tärkeää:** Poista `setup.php` asennuksen jälkeen turvallisuussyistä!

```bash
rm setup.php
```

### 5. Aseta oikeudet

```bash
chmod 755 storage/logs
chmod 755 public/assets/uploads
chmod 644 config/config.php
```

## 🚀 Käyttöönotto

### Paikallinen kehitys

PHP:n sisäänrakennettu palvelin:

```bash
cd public
php -S localhost:8000
```

Avaa selaimella: `http://localhost:8000`

### Tuotanto

1. Aseta webpalvelin (Apache/Nginx) kuten yllä
2. Varmista että `.htaccess` on käytössä (Apache)
3. Aseta `display_errors = Off` tuotannossa
4. Käytä HTTPS-yhteyttä
5. Varmuuskopioi tietokanta säännöllisesti

## 📁 Projektirakenne

```
huuto/
├── public/                 # Julkinen kansio (DocumentRoot)
│   ├── index.php          # Pääsisääntulopiste
│   ├── .htaccess          # Apache URL rewrite
│   └── assets/
│       ├── css/
│       │   └── style.css  # Päätyylitiedosto
│       ├── js/
│       │   └── main.js    # JavaScript-toiminnot
│       ├── img/           # Kuvat
│       └── uploads/       # Käyttäjien lataamat tiedostot
│
├── app/                    # Sovelluslogiikka
│   ├── Router.php         # URL-reititys
│   ├── Database.php       # Tietokantayhteys
│   ├── Security.php       # Turvatoiminnot
│   ├── controllers/       # Kontrollerit
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── CategoryController.php
│   │   ├── ListingController.php
│   │   ├── BidController.php
│   │   ├── UserController.php
│   │   ├── SearchController.php
│   │   ├── PageController.php
│   │   ├── BlogController.php
│   │   └── AdminController.php
│   └── views/             # Näkymätemplatet
│       ├── layout.php     # Päälayout
│       ├── home.php
│       ├── auth/          # Autentikointi
│       ├── categories/    # Kategoriat
│       ├── listings/      # Ilmoitukset
│       ├── user/          # Käyttäjä
│       ├── admin/         # Admin
│       └── blog/          # Blogi
│
├── config/                 # Asetukset
│   └── config.php         # Tietokanta-asetukset (luodaan setupissa)
│
├── storage/                # Tallennustila
│   └── logs/              # Lokit
│
├── schema.sql             # Tietokantaskeema
├── setup.php              # Asennusohjelma
├── .htaccess              # Root-tason rewrite
├── .gitignore
└── README.md              # Tämä tiedosto
```

## 👥 Testikäyttäjät

### Admin-käyttäjä
Asennusohjelman aikana luotu admin-tili (määrität itse).

Oletus-ehdotus:
- **Sähköposti**: `admin@huuto.local`
- **Salasana**: *(määrität asennuksessa)*

### Demo-käyttäjä
Automaattisesti luotu testikäyttäjä:
- **Sähköposti**: `demo@huuto.local`
- **Salasana**: `demo123`

## 🎯 Ominaisuudet yksityiskohtaisesti

### Kategoriat (21 kpl)

1. 📍 Maakunnittain
2. 🚗 Ajoneuvot ja tarvikkeet
3. 🚜 Työkoneet ja raskas kalusto
4. 🏠 Asunnot, mökit, toimitilat ja tontit
5. ⚽ Harrastusvälineet ja vapaa-aika
6. 🌳 Piha ja puutarha
7. 🔧 Työkalut ja työkalusarjat
8. 🏗️ Rakennustarvikkeet
9. 🛋️ Sisustaminen ja koti
10. 💻 Elektroniikka
11. 🎨 Keräily
12. 📦 Tukkuerät
13. 📌 Muut
14. ⚖️ Perinteiset huutokaupat
15. ⚖️ Ulosotto
16. 💼 Konkurssipesät
17. 🎖️ Puolustusvoimat
18. 🌲 Metsähallitus
19. 💰 Rahoitusyhtiöt
20. 🏛️ Julkinen sektori
21. ⏰ Päättyvät

### Staattiset sivut

- Tietoa palvelusta
- Tietoa huutajalle
- Käyttöehdot
- Aloita myyminen
- Myyntiehdot
- Hinnasto
- Maksutavat
- Asiakaspalvelu
- Ohjeet ja vinkit
- Yritys / Tietoa meistä
- Meille töihin
- Medialle
- Tietosuojaseloste
- Saavutettavuusseloste
- Läpinäkyvyysraportti

### Huutokauppatoiminnot

1. **Ilmoituksen luonti**
   - Otsikko, kuvaus, kategoria
   - Kunto, sijainti (maakunta)
   - Aloitushinta, minimikorotus
   - Valinnainen "Osta heti" -hinta
   - Kesto (1-30 päivää)
   - Max 8 kuvaa

2. **Huutaminen**
   - Automaattinen minimikorotuksen tarkistus
   - Estää huutamisen omaan ilmoitukseen
   - Estää huutamisen päättyneeseen
   - Reaaliaikainen ajastin
   - Huutohistoria

3. **Ilmoituksen seuranta**
   - Omat ilmoitukset
   - Omat huudot (johdossa/ei johdossa)
   - Omat voitot

## 🔒 Turvallisuusominaisuudet

1. **Salasanat**: `password_hash()` ja `password_verify()` PHP-funktiot
2. **CSRF**: Token kaikissa POST-lomakkeissa
3. **SQL Injection**: Prepared statements (PDO)
4. **XSS**: `htmlspecialchars()` kaikessa käyttäjäsyötteessä
5. **Rate Limiting**: IP-pohjainen kirjautumisyritystemittaus
6. **Session Security**: Turvallinen session-hallinta
7. **Access Control**: Roolipohjainen pääsynhallinta

## 🛠️ Kehitys

### Tietokantarakenne

Katso yksityiskohdat: `schema.sql`

Taulut:
- `users` - Käyttäjät
- `categories` - Kategoriat
- `listings` - Ilmoitukset
- `listing_images` - Ilmoitusten kuvat
- `bids` - Huudot
- `pages` - Staattiset sivut
- `blog_posts` - Blogikirjoitukset
- `login_attempts` - Kirjautumisyritykset (rate limiting)

### URL-rakenne

```
/                           - Etusivu
/haku                       - Haku
/kategoriat                 - Kategoriat
/kategoria/{slug}           - Kategoriaan kuuluvat ilmoitukset
/kohde/{id}/{slug}          - Yksittäinen ilmoitus
/paattyvat                  - Päättyvät pian

/kirjaudu                   - Kirjautuminen
/rekisteroidy               - Rekisteröityminen
/kirjaudu-ulos              - Uloskirjautuminen
/vahvista/{token}           - Sähköpostivahvistus

/profiili                   - Käyttäjäprofiili
/omat-ilmoitukset           - Omat ilmoitukset
/omat-huudot                - Omat huudot
/omat-voitot                - Omat voitot
/luo-ilmoitus               - Luo ilmoitus

/huuda/{id}                 - Huuda (POST)

/sivu/{slug}                - Staattinen sivu
/blogi                      - Blogi
/blogi/{slug}               - Blogikirjoitus

/admin                      - Admin-paneeli
/admin/kayttajat            - Käyttäjien hallinta
/admin/ilmoitukset          - Ilmoitusten hallinta
/admin/kategoriat           - Kategorioiden hallinta
/admin/blogi                - Blogin hallinta
```

## 🐛 Vianmääritys

### "404 Not Found" kaikilla sivuilla paitsi etusivulla

**Apache**: Varmista että `mod_rewrite` on käytössä ja `AllowOverride All` on asetettu.

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**Nginx**: Tarkista että `try_files` on oikein konfiguroitu.

### "Database connection failed"

1. Tarkista tietokannan yhteystiedot `config/config.php`
2. Varmista että MySQL/MariaDB on käynnissä
3. Tarkista että käyttäjällä on oikeudet tietokantaan

### Kuvat eivät lataudu

1. Tarkista että `public/assets/uploads/` -kansio on olemassa
2. Varmista että kansiolla on kirjoitusoikeudet (755)
3. Lisää placeholder-kuvat `public/assets/img/` -kansioon

### "Setup already installed"

Poista `config/config.php` tai käytä `?force=1` parametria URLissa uudelleenasennukseen.

## 📝 Lisenssi

Tämä on koulutus/demo-projekti. 

## 🤝 Yhteystiedot

Projekti: https://github.com/AnomFIN/huuto

---

**Huom!** Tämä on demo/kehitysprojekti. Tuotantokäytössä suositellaan:
- HTTPS-yhteys
- Vahvempi autentikointi (2FA)
- Email-palvelu oikeiden vahvistusten lähettämiseen
- Kuvien pakkaus ja optimointi
- CDN kuvien jakeluun
- Välimuisti (Redis, Memcached)
- Virheenseuranta (Sentry, ym.)
- Varmuuskopiointi
- Monitoring
