## About ITS-DB

Jeder Techniker steht irgendwann vor dem Problem seine eigenen Informationen zu jedem Projekt notieren und irgendwie sortieren zu müssen. Bei mir artete das in einem gewusel aus Ordnern und vielen Dateien aus.

Leider hat das bei mir keine wirklich guten Ergebnisse erzielt. Also habe ich diese Datenbank erschaffen. Joa...

## Features

- Übersicht
  - Offene Projekte
  - Neu hinzugefügte Kunden
- Customers
  - Übersicht aller Kunden
- Kunden Ansicht
  - Server mit jeweiligen Informationen
  - Bemerkungen zum Kunden (Hier können besonderheiten erfasst werden)
  - Credentials zum Projekt
  - Hinzufügen von Kontaktinformationen.
- Server Ansicht
  - Serverinformationen hinzufügen (IP, FQDN, etc)
  - Produkt Compose hinzufügen
    - docker-compose Inhalte generieren
    - env informationen aus docker-compose generieren
  - Speichern und Validieren von Zertifikaten
- Compose Ansicht
  - Aktualisieren der Compose Original Files
  - Anpassen der Container Dokumentationen

## Vorraussetzungen während der Entwicklungsphase

Jeder, der diese Software während der Entwicklungsphase nutzen will, muss 2 Tools installieren.

1. PHP >= 8.1
   - Link (Windows): https://windows.php.net/downloads/releases/php-8.2.5-nts-Win32-vs16-x64.zip
   - Den Inhalt der ZIP datei in einen beliebigen Ordner packen. Beispielsweise c:\PHP
   - Danach wie hier in der Anleitung die Umgebungsvariable (PATH) erweitern:
     - https://www.forevolve.com/en/articles/2016/10/27/how-to-add-your-php-runtime-directory-to-your-windows-10-path-environment-variable/
2. git >= 2.39.2 (einfach mal auf der CMD prüfen ob das schon vorhanden ist oder nicht.)
   - Link (Windows): https://github.com/git-for-windows/git/releases/download/v2.40.0.windows.1/Git-2.40.0-64-bit.exe
   - Im Prinzip einfach durch installieren. Wenn gefragt wird ob der bin Ordner oder git in PATH hinzugefügt werden soll, dann einfach bestätigen. Das vereinfacht alles später IMMENS! 

## Installation des Scripts während der Entwicklungsphase

- Erstelle irgendwo einen Ordner, wo das Script installiert werden soll.
- Öffne eine CMD und springe in den entsprechenden Ordner
- führe folgenden Befehl aus:
  - git clone https://github.com/rygos/itsdb.git .
- Wichtig ist hier wirklich, das hinten ein . steht. Ohne den würde in dem erstellen Verzeichnis ein weiteres erstellt werden. Ist nicht schlimm, sieht aber unschön aus.
- Das script ist nun installiert. Leider kann es an der Stelle noch nicht sinnvoll gestartet werden. Dafür muss vorher noch die .env in das Basisverzeichnis erstellt werden. Den Inhalt erhaltet ihr dann von mir.
- Ist die .env hinterlegt, kann das Script mit folgendem Befehl gestartet werden:
  - php artisan serve
- Im Konsolenfester wird nach kurzer wartezeit ein Pfad wie http://127.0.0.1:8000 angezeigt. Dies ist, so lange das Konsolenfester geöffnet ist, der Pfad der im Browser eingegeben werden muss um das Script zu nutzen.
- Sollte der 8000er Port von irgendeinem anderen Programm schon genutzt werden oder nicht gewünscht sein, dann kann dieser auf folgende Art selbst gewählt werden:
  - php artisan serve --port=9000

## Update des Scripts während der Entwicklungsphase

- Öffnen vom CMD und springen in das Scriptverzeichnis
- Ausführen des folgenden Befehls:
  - git pull
- Wenn das ohne Fehler durch läuft, dann ist alles gut. Sollten fehler auftauchen, dann einfach bei mir melden. Nur in DIESEM Fall ist kein Issue hier auf Github nötig. 
