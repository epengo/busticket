# busticket használat

## Beüzemelés docker segítségével

1. Docker telepítés:

  [https://docs.docker.com/engine/install/](https://docs.docker.com/engine/install/)

2. git clone https://github.com/epengo/busticket.git

3. cd busticket

4. Buildelés és indítás (a terminálban látni fogjuk az apache szerver logjait).
```
docker build . -t app && docker run -p 8000:80 app
```

6. Megnyitás böngészőben: [http://localhost:8000](http://localhost:8000)

## beüzemelés XAMPP/WAMP segítségével

1. XAMPP/WAMP telepítése megfelelő PHP verzióval

2. A XAMPP/WAMP `php.ini` fájlában az `sqlite3` és a `gd` bővítmények kikommentezése

3. A források letölése zipként vagy git klónozással
```
git clone https://github.com/epengo/busticket.git 
```
4. A forrás mappájának elhelyezése a XAMPP `htdocs` mappájában vagy a WAMP `www` mappájában.

5. Megfelelő jogosultság beállítása a db mappára és tartalmára, hogy a webszerveren keresztül írni is lehessen.

6. Megnyitás böngészőben: [http://localhost/busticket](http://localhost/busticket)
