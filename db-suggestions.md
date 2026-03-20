# Adatbázis struktúra módosítás javaslatok

<!-- ez a fájl egy nem kötelező feladat része -->
1. A minimum hőmérséklet, maximum hőmérséklet és csapadék táblák külön vannak, miközben lehetnének egy táblában is, így megspórolva több JOIN műveletet.

2. A dátumok nem DATE típusúak.

3. A 002_import_city_data fájlban Szombathely kétszer van (9., 10.), a 003_import_weather_data fájlban pedig a 10. helyen Túrkeve van, ez a lekérdezésekben is zavaros.