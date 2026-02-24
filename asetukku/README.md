# Asetukku — FINAL frontend (staattinen konseptiversio)

Tämä paketti on **pelkkä frontend** (ei backendia, ei tietokantaa). Kaikki “toiminnot” (ostoskori, haku, kassa, lomakkeet, kirjautuminen) toimivat selaimessa **localStorage**n avulla.

## Käyttö

### 1) Suositus: aja pienenä staattisena palvelimena
Selaimen turvallisuus rajoittaa `fetch()`-kutsuja jos avaat sivun suoraan `file://`-polusta.

**Linux / macOS**
```bash
cd asetukku_final
python3 -m http.server 8080
```
Avaa selaimessa: `http://localhost:8080`

**Windows**
```bat
cd asetukku_final
py -m http.server 8080
```

### 2) Julkaisu webhotellille
Voit uploadata sisällön sellaisenaan (esim. `public_html/`).

## Sisältö
- Kirjautuminen: esittelyssä nopeutettu (onnistuu aina)
- Ostoskori + kassaputki: luo konseptiversio-tilauksen localStorageen
- “Myy aseesi meille” ja “Ilmoita aseesi myyntiin”: tallentuu localStorageen ja näkyy tilillä
- Teema: vaalea/tumma

## Kuvitus
Kuvat ladataan verkosta (Wikimedia Commons / vapaalisenssit). Lähteet: `credits.html`.
