# 🚀 Huuto247.fi Käyttöönotto-ohje FTP-palvelimelle

## 📋 Käyttöönotto MySQL-tietokannalla

### 1. Tietokanta-asetukset (phpMyAdmin)

1. **Kirjaudu phpMyAdmin:iin**
2. **Luo uusi tietokanta** nimeltä `huuto247_prod` (tai käytä olemassaolevaa)
3. **Aja SQL-script** `database/mysql_production_schema.sql`
   - Avaa tiedosto phpMyAdmin:ssa (Import -välilehti)
   - Tämä luo kaikki tarvittavat taulut ja peruskategoriat

### 2. Konfiguraatiotiedosto

1. **Kopioi** `config/production.example.php` → `config/production.php`
2. **Päivitä MySQL-asetukset** `config/production.php` tiedostoon:
   ```php
   define('DB_HOST', 'localhost');           // Palvelimen MySQL-osoite
   define('DB_NAME', 'huuto247_prod');       // Tietokannan nimi
   define('DB_USER', 'käyttäjätunnus');      // MySQL-käyttäjä
   define('DB_PASS', 'salasana');            // MySQL-salasana
   ```

### 3. FTP-lataus

**Lataa FTP:llä** kaikki tiedostot palvelimen web-hakemistoon:
- ❗ **ÄLÄ lataa** `/database/huuto247.db` (SQLite-tiedosto) - ei tarvita tuotannossa
- ✅ **Lataa kaikki muut tiedostot**

### 4. Oikeudet ja turvallisuus

Varmista että seuraavilla hakemistoilla on kirjoitusoikeudet:
```
chmod 755 /logs/
chmod 755 /uploads/
chmod 755 /uploads/auctions/
chmod 755 /uploads/avatars/
```

### 5. Testaa toiminta

Vieraile osoitteessa: `https://huuto247.fi`
- ✅ Etusivu latautuu ja näyttää auktioita
- ✅ Kategoriat-sivu toimii: `https://huuto247.fi/category.php`
- ✅ Yksittäiset auktiot aukeavat: `https://huuto247.fi/auction.php?id=1`

## 📝 Tietokannan rakenne

**MySQL-taulut luodaan automaattisesti:**
- `categories` - Tuotekategoriat (10 peruskategoriaa)
- `users` - Käyttäjätilit
- `auctions` - Huutokaupat
- `bids` - Tarjoukset
- `auction_images` - Tuotekuvat
- `watchlist` - Seurantalistat
- `auction_metadata` - Lisätiedot

## 🔧 Vianmääritys

**Jos sivusto ei lataudu:**
1. Tarkista `config/production.php` MySQL-asetukset
2. Varmista että tietokanta ja taulut on luotu phpMyAdmin:issa  
3. Tarkista `/logs/php_errors.log` virhelokista

**Jos auktiot eivät näy:**
1. Lisää esimerkkiauktioita phpMyAdmin:issa:
   ```sql
   INSERT INTO auctions (user_id, category_id, title, starting_price, end_time, status, featured) 
   VALUES (1, 1, 'Testituote', 100.00, DATE_ADD(NOW(), INTERVAL 7 DAY), 'active', 1);
   ```

## 📧 Ylläpito

- **Lokitiedosto:** `/logs/php_errors.log`
- **Konfiguraatio:** `/config/production.php`
- **Tietokanta:** phpMyAdmin kautta