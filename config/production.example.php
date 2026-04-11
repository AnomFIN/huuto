<?php
/**
 * Tuotantopalvelimen MySQL-tietokanta-asetukset
 * 
 * OHJE KÄYTTÖÖNOTTOON:
 * 1. Kopioi tämä tiedosto app/config.php nimellä (poista .example)
 * 2. Päivitä alla olevat tiedot vastaamaan palvelimesi MySQL-asetuksia
 * 3. Lataa mysql_production_schema.sql phpMyAdmin:iin tietokannan luomiseksi
 */

// MySQL-tietokanta-asetukset (päivitä nämä oikeiksi arvoiksi!)
define('DB_HOST', 'localhost');           // Tietokantapalvelimen osoite
define('DB_NAME', 'huuto247_prod');       // Tietokannan nimi
define('DB_USER', 'huuto247_user');       // MySQL-käyttäjätunnus  
define('DB_PASS', 'VAIHDA_TÄMÄ_SALASANA'); // MySQL-salasana
define('DB_CHARSET', 'utf8mb4');

// Sivusto-asetukset
define('SITE_URL', 'https://huuto247.fi');
define('SITE_NAME', 'Huuto247');

// Virheenkäsittely tuotannossa
ini_set('display_errors', 0);  // Ei näytetä virheitä käyttäjille
ini_set('log_errors', 1);      // Tallennetaan virheet lokiin
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// Sessio-asetukset
ini_set('session.cookie_secure', 1);     // HTTPS-pakotus
ini_set('session.cookie_httponly', 1);   // JavaScript ei pääse käsiksi
ini_set('session.use_strict_mode', 1);   // Turvalliset session ID:t

// Palvelimen aika-alue
date_default_timezone_set('Europe/Helsinki');
?>