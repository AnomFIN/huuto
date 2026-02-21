# 🔧 Auctions Not Showing - Quick Fix

## Ongelma
Huutokohteet eivät näy etusivulla tai missään muualla, vaikka `auctions.sql` sisältää dataa.

## Syy
`auctions.sql` viittaa käyttäjään `user_id=2` ja kategorioihin `category_id=1-29`, mutta jos `users` tai `categories` taulut ovat tyhjät, INNER JOIN ei palauta tuloksia.

## 🚀 Nopea korjaus

### Vaihtoehto 1: SQL-skripti (suositeltu)
```sql
-- Suorita fix_data.sql tietokannassa
source fix_data.sql;
```

### Vaihtoehto 2: PHP-skripti
```bash
php fix_missing_data.php
```

### Vaihtoehto 3: Koko tietokannan setup
```
http://localhost/setup_complete_db.php
```

## 🔍 Debuggaus

Katso mikä on vikana:
```
http://localhost/debug_auctions.php
```

## ✅ Mitä korjaus tekee

1. **Lisää käyttäjän id=2** - "Demo Myyjä" johon kaikki huutokohteet viittaavat
2. **Lisää kategoriat 1-29** - kaikki kategoriat joita `auctions.sql` käyttää  
3. **Muuttaa JOIN:it** - `INNER JOIN` → `LEFT JOIN` jotta data näkyy vaikka jokin puuttuisi
4. **Testaa toiminnallisuuden** - varmistaa että huutokohteet näkyvät

## 📋 Odotettu tulos

Etusivulla pitäisi nyt näkyä:
- Harley Davidson -moottoripyörä (€4,000)
- Makita imuri (€80-90)  
- Custom Chopper Motorcycle (€15,000)
- Kaivinkone Liebherr (€10,000)
- Ja muita aitoja huutokohteita

## 🛠️ Muutetut tiedostot

- `src/models/Auction.php` - LEFT JOIN käyttöön
- `fix_data.sql` - kategoriat ja käyttäjä
- `debug_auctions.php` - debuggaussivu
- `setup_complete_db.php` - koko setup

Nyt huutokohteet näkyvät etusivulla! 🎉