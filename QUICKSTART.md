# Huuto - Pika-aloitusohje

## Nopea asennus

### 1. Vaatimukset
- PHP 8.0+
- MySQL/MariaDB
- Webpalvelin (Apache/Nginx)

### 2. Asenna tietokanta

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

### 3. Käynnistä PHP:n kehityspalvelin

```bash
cd public
php -S localhost:8000
```

### 4. Avaa selaimella

Mene osoitteeseen: http://localhost:8000/setup.php

### 5. Seuraa asennusohjelmaa

1. **Vaihe 1**: Syötä tietokannan tiedot
   - Host: localhost
   - Database: huuto
   - User: huuto_user
   - Password: your_password

2. **Vaihe 2**: Luo admin-käyttäjä
   - Nimi: Admin
   - Email: admin@huuto.local
   - Salasana: (valitse vahva salasana)

3. **Vaihe 3**: Asennus valmis!

### 6. Testaa

Nyt voit:
- Kirjautua admin-tilillä: admin@huuto.local
- Kirjautua demo-tilillä: demo@huuto.local / demo123
- Selata kategorioita
- Luoda ilmoituksia
- Huutaa ilmoituksiin

## Tärkeää

- **Poista setup.php** asennuksen jälkeen!
- Vaihda admin-salasana heti ensimmäisen kirjautumisen jälkeen
- Tuotannossa käytä HTTPS:ää

## Ominaisuudet

✅ 21 kategoriaa ilmoituksille
✅ 63+ valmista testilmoitusta
✅ Käyttäjärekisteröinti ja kirjautuminen
✅ Huutojärjestelmä
✅ Admin-paneeli
✅ Responsiivinen design
✅ Turvallisuusominaisuudet (CSRF, Rate limiting, jne.)

## Tuki

Katso täydelliset ohjeet: README.md
