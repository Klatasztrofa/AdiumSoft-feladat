# Adiumsoft PHP / React gyakorlati feladat

## Előkészületek

Legyenek feltelepítve az alábbi programok:

- [Docker](https://docs.docker.com/get-started/get-docker/) és [Docker Compose](https://docs.docker.com/compose/install/)
- `git` valamilyen formájában (pl. [Git bash](https://git-scm.com/downloads/win))

A leírás további részében szereplő parancsok `bash`-hoz készültek, ebben fognak
helyesen működni. Használható természetesen `cmd`, PowerShell vagy bármilyen
egyéb shell is, de ebben az esetben valószínüleg a parancsok módosítására lesz
szükség. 

Ha még nem tetted meg, töltsd le magadhoz a repository-t:

```sh
git clone https://bitbucket.org/adiumsoftdev/takehome.git
```

Hozd létre a `.env` fájlt - ez tartalmazza a Docker containerek beállításait:

```sh
cd takehome
cp .env.example .env
```

## Feladat

A feladat során meteorológiai adatokból kimutatást készítő fullstack alkalmazást
kell elkészítened. A részfeladatok megoldási sorrendje tetszőleges. A
végeredmény ellenőrzése a `prod` és `db` Docker containerek felépítésével,
indításával, használatával, illetve a forráskód ellenőrzésével fog zajlani. A
feladatot tehát akkor add át, mikor megbizonyosodtál róla, hogy az alábbi
parancs lefut,

```sh
docker compose up prod db
```

az elkészült program elérhető a `http://localhost:8300` címen, és megfelelően
működik. Csak az `api/` és `client/` mappák tartalmát, a `db-suggestions.md`
fájlt, illetve szükség esetén a `docker-compose.yaml` fájl tartalmát módosítsd!


### Fejlesztői környezet, Docker

Készítsd elő a feladathoz szükséges adatokat! Az adatok a `migrations/`
könyvtárban, a `data.zip` fájlban találhatóak. Csomagold ki ugyanebbe a mappába
atartalmát. A `db` container indításakor ezek a fájlok automatikusan be lesznek
töltve.

```sh
cd migrations
unzip data.zip
```

Indítsd el a fejlesztéshez szükséges Docker containereket:

```sh
docker compose up api-dev client-dev db
```

Sikeres indítás után győződj meg róla, hogy lefutottak-e a külső csomagokat
telepítő scriptek (`composer install` és `npm ci`)! A Docker folyamatnak legyen
írási joga az `api/` és `client/` mappákhoz, mert a telepítő scriptek írás
műveletet végeznek. Ha ezek a mappák nem írhatóak számára, nem fognak sikeresen
lefutni. Akkor jó, ha ehhez hasonló üzeneteket találsz a konzolban (nem
feltétlenül közvetlenül egymás után):

```
takehome-db          | [INFO] /docker-entrypoint-initdb.d/tx_o_Budapest_19012023.csv adatok importálása sikeres
takehome-db          | 2026-03-06T13:31:41.803028Z 0 [System] [MY-010931] [Server] /usr/sbin/mysqld: ready for connections.
takehome-client-dev  |   VITE v7.3.1  ready in 149 ms
takehome-client-dev  |
takehome-client-dev  |   ➜  Local:   http://localhost:5173/
takehome-api-dev     | 2026-03-06 13:31:45,850 INFO exited: composer-install (exit status 0; expected)
```

Az `api-dev` container működése ellenőrizhető az alábbi paranccsal:

```sh
$ curl localhost:8080/hello?name=vendég

{"message":"Hello, vendég!"}
```

A `client-dev` containeré pedig ezzel:

```sh
$ curl localhost:5173

<!doctype html>
<html lang="hu">

<head>
# stb.
```

Ha ezen a ponton nem találod a fentebb említett üzeneteket, az ellenőrző
parancsokra nem ezeket a válaszokat kapod, vagy más hibát tapasztalsz, azt
jelezd felénk! [hiring@adiumsoft.hu](mailto:hiring@adiumsoft.hu)

A mintaadatokat egy MySQL adatbázisban tároljuk, amely a host gépről a `8081`
porton érhető el. A használatához szükséges felhasználónév `root`, a jelszó
szintén `root`. Természetesen a containeren belülről is használható:

```sh
docker compose exec db sh -c 'mysql --user=root --password=root'
```

A forráskódban változó-, függvény-, függvényparaméter- és mezőnevek esetén
camelCase írásmódot használj. Osztályok, típusok, React komponenesek nevei
legyenek PascalCase módon formázva.

### Szerveroldal

A szerveroldalon PHP-t használj, vedd igénybe az előre elkészített
[keretrendszer komponenseket](./api/src/Framework) a HTTP kérések kezeléséhez és az
adatbázis-lekérdezésekhez. Készítsd el az alábbi két végpontot:

1. Query paraméterként város azonosítót, kezdő- és végdátumot fogad és
   visszaadja a város időjárási adatait. 

2. Query paraméterként _régiómegnevezést_ (pl. Dunántúl), kezdő- és végdátumot
   fogad és visszaadja a megadott régió városainak _átlagolt_ időjárási adatait.

Az adatokból (kliensoldalon) olyan táblázatot kell készíteni, amelyben
feltünteted a minimum-, maximum- és átlaghőmérsékletet, valamint a csapadék
adatokat, valamennyi esetben mértékegységgel együtt. A táblázat láblécében fel
kell tüntetned az egyes oszlopok medián- és módusz értékét. 

A végpontok, query paraméterek neve és a visszaadott adatok struktúrája szabadon
megválasztható, a cél az adatok megjelenítése. A végpontok válasza JSON
formátumú szöveg legyen. 

A fent említett végpontokon kívül továbbiak és létrehozhatóak, amennyiben
szükségesnek ítéled.

### Kliensoldal

Kliensoldalon a UI létrehozásához használd a React könyvtárat, a TypeScript
nyelvet és a Vite build rendszert. Hozz létre két felületet:

1. Egy várost, illetve egy kezdő- és végdátumot lehet rajta kiválasztani. Egy
   gomb megnyomására letölti a város adott időszakbeli időjárás adatait és
   táblázatos formában jeleníti meg. A táblázat oszlopai legyenek a minimum-,
   maximum- és átlaghőmérséklet, valamint a csapadék adatok. Az értékek mellett
   a megfelelő mértékegységet is tüntesd fel.

1. Egy _régiót_, illetve egy kezdő- és végdátumot lehet rajta kiválasztani. Egy
   gomb megnyomására letölti a régió adott időszakbeli _átlagolt_ időjárás adatait és
   táblázatos formában jeleníti meg. A táblázat oszlopai legyenek a minimum-,
   maximum- és átlaghőmérséklet, valamint a csapadék adatok. Az értékek mellett
   a megfelelő mértékegységet is tüntesd fel.

Az átlaghőmérsékletet a minimum- és maximum hőmérséklet adatokból számold ki! Az
oldalak elérési módja tetszőleges - használhatsz bármilyen routing könyvtárat
(pl. [TanStack Router](https://tanstack.com/router/latest)), de anélkül is
tökéletesen megfelelő. A felületek formázása szintén tetszőleges, de ne a
böngésző által biztosított alapértelmezett kinézet legyen. Lehetőség szerint
használd a Tailwind CSS keretrendszert a formázáshoz (de nyers CSS is jó).

### Extra feladatok

Ezek a feladatok nem kötelezőek, de rajtuk keresztül be tudod mutatni az egyes
technológiák mélyebb ismeretét.

1. A felhasználói felületen lehessen váltani SI és angolszász mértékegységek
   (Celsius helyett Fahrenheit, mm helyett inch) között!

1. Írj automata teszteket a szerveroldali működéshez PHPUnit segítségével!

1. Vizsgáld meg az adatbázis felépítését és tartalmát. Fogalmazz meg
   javaslatokat a hibásnak, javítandónak vélt részekkel kapcsolatban és írd le
   őket a `db-suggestions.md` fájlba.

1. Használd a PHP forráskód formázó és statikus elemző parancsokat (`composer
   format`, `composer analyse`). Javítsd ki az esetleges statikus elemző által
   jelzett hibákat!
