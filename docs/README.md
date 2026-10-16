<!-- Screenshots mit 1300px Breite erstellt -->

### Beschreibung

Das Apple HomeKit-Modul stellt Variablen aus Symcon als Geräte in Apple HomeKit bereit. Diese können über die Home-App, über Siri (z.B. auf HomePods, iPads und iPhones) und in HomeKit-Automationen verwendet werden. Änderungen in Symcon werden dabei direkt an HomeKit gemeldet.

### Voraussetzungen

* Symcon 9.1 oder neuer
* Ein iPhone oder iPad mit der Home-App im selben Netzwerk wie der Symcon Server
* Für die Nutzung außerhalb des eigenen Netzwerks und für Automationen: eine HomeKit-Steuerzentrale, also ein dauerhaft eingeschalteter HomePod oder ein Apple TV

### Besonderheiten bei der Verwendung mit Docker

Um HomeKit korrekt mit Docker verwenden zu können, muss das Netzwerk des Containers im Host-Modus konfiguriert sein. Im standardmäßig ausgewählten Bridge-Modus hat der Container ansonsten keine korrekt im internen Netzwerk erreichbare IP-Adresse (sondern nur eine hinter dem Docker NAT) und somit werden die notwendigen Bonjour/DNS-SD Pakete nicht empfangen. Soll Symcon mit Docker auf Synology/QNAP verwendet werden, so ist zusätzlich darauf zu achten, dass Bonjour/DNS-SD auf dem NAS deaktiviert ist, sodass der notwendige Port für den Docker Container zur Verfügung steht.

### Einbindung in Symcon

#### Modul installieren

Damit das Apple HomeKit-Modul verwendet werden kann, muss es zuerst über das [Module Control][modulecontrol] installiert werden. Hierfür muss das [Module Control][modulecontrol] geöffnet werden. Dies befindet sich unter der Hauptkategorie "Kern Instanzen" im Objektbaum oder kann direkt über das Widget "Aktualisierungen" geöffnet werden.
Zur Installation im [Module Control][modulecontrol] den Hinzufügen-Button unten rechts betätigen. Im sich öffnenden Dialog die URL https://github.com/paresy/HomeKit eingeben und mit "OK" bestätigen.

![Modul installieren][module]

#### Apple HomeKit-Instanz erstellen

Als nächstes eine [Instanz][instanzen] vom Apple HomeKit-Modul erstellen. Zuerst den Objektbaum öffnen. Hier den Hinzufügen-Button unten rechts betätigen und [Instanz][instanzen] auswählen.

![Instanz hinzufügen][add-instance]

In der Modulliste das Gerät "HomeKit Bridge" vom Hersteller "Apple" auswählen. Der Ort sollte nicht verändert werden, der Name kann beliebig gewählt werden. Abschließend mit "OK" bestätigen.

![Instanz erstellen][create-instance-1]

Jede HomeKit Bridge benötigt einen eigenen Server Socket, über den die Home-App mit der Bridge kommuniziert. Beim Erstellen schlägt Symcon dafür automatisch einen neuen Server Socket vor. Diesen erstellen lassen und aktiv schalten. Der Port wird von der Bridge vorgegeben (Standard: 34587).

<!-- Screenshot fehlt: Vorschlag des Server Sockets beim Erstellen der Instanz -->

Nach der Erstellung der Apple HomeKit-Instanz öffnet sich diese automatisch und kann eingerichtet werden.

#### Geräte einrichten

Alle Geräte werden in der Liste "Geräte" der Instanz verwaltet. Um ein Gerät hinzuzufügen, unter der Liste auf "Hinzufügen" klicken. Im Dialog wird zuerst ein Name vergeben, unter welchem das Gerät in HomeKit erscheint. Danach wird der Typ des Geräts ausgewählt. Abhängig vom Typ erscheinen darunter die benötigten Felder, z.B. die zu verwendende Variable. Welche Variablen ein Typ benötigt, steht auf der jeweiligen Seite der Dokumentation. Mit "OK" wird der Eintrag übernommen.

<!-- Screenshots fehlen: Liste "Geräte" sowie Dialog mit Name, Typ und den Feldern des Typs -->

Jeder Eintrag erhält automatisch eine fortlaufende ID. Über diese ID erkennt HomeKit das Gerät wieder, sie wird daher nicht verändert. Der Name und die Variablen können jederzeit über das Bearbeiten-Symbol des Eintrags geändert werden.

Nachdem alle Geräte eingerichtet sind, werden diese mit einem Klick auf "Änderungen übernehmen" gespeichert. HomeKit erkennt die Änderungen selbstständig, ein erneutes Koppeln ist nicht nötig.

Nach dem Übernehmen sollte sichergestellt werden, dass bei allen Geräten "OK" in der Spalte "Status" steht. Nur diese Geräte sind in HomeKit verfügbar. Ist dies nicht der Fall, so beschreibt die Spalte das Problem:

Status                                  | Bedeutung
--------------------------------------- | ---------
OK                                      | Das Gerät ist in HomeKit verfügbar
Variable fehlt                          | Es wurde keine Variable ausgewählt oder die Variable wurde gelöscht
Boolean benötigt, Integer benötigt, ... | Die Variable hat nicht den benötigten Variablentyp
Aktion benötigt                         | Die Variable hat keine Aktion und kann daher nicht geschaltet werden
Profil benötigt                         | Die Variable hat weder ein Profil noch eine Darstellung, aus der sich der Wertebereich ergibt
Limit überschritten                     | Das Gerät liegt über dem Limit von 149 Geräten pro Bridge, siehe unten

Bei einigen Geräten mit mehreren Variablen nennt die Meldung zusätzlich das betroffene Feld, z.B. "TargetTemperatureID: Aktion benötigt". Meldungen, die nur ein bestimmter Gerätetyp kennt, sind auf dessen Seite beschrieben.

_Bestehende Konfigurationen älterer Versionen werden beim Update automatisch übernommen. Die Geräte bleiben dabei in HomeKit erhalten._

#### Anforderungen an Variablen

Damit HomeKit ein Gerät schalten kann, muss die Variable _schaltbar_ sein, also eine Aktion besitzen. Das ist bei Variablen von Geräte-Instanzen in der Regel bereits der Fall. Bei eigenen Variablen kann eine Aktion in den Eigenschaften der Variable gesetzt werden.

Je nach Gerätetyp werden folgende Arten von Variablen verwendet:

Art               | Anforderungen
----------------- | -------------
Schaltvariable    | Eine Variable vom Typ Boolean. Bei einem Profil mit der Endung ".Reversed" werden die Werte umgekehrt.
Dimmbare Variable | Eine Variable vom Typ Integer oder Float mit einem Wertebereich. Dieser ergibt sich aus der Darstellung "Schieberegler", "Wertanzeige" oder "Rollladen" bzw. aus Minimal- und Maximalwert des Profils. HomeKit nutzt immer 0-100 %, was auf diesen Wertebereich umgerechnet wird. Bei einem Profil mit der Endung ".Reversed" sowie bei einer Rollladen-Darstellung, deren Wert für "Geschlossen" kleiner ist als der für "Offen", wird der Wert umgekehrt.
Rollladenvariable | Eine Variable vom Typ Integer mit dem Profil ~ShutterMoveStop oder ~ShutterMoveStep (0 = Öffnen/Hoch, 2 = Stopp, 4 = Schließen/Runter).

#### Mögliche Gerätetypen

* [Lampe (Schaltbar)][lampe-schaltbar]
* [Lampe (Dimmbar)][lampe-dimmbar]
* [Lampe (Farbig)][lampe-farbig]
* [Lampe (Experte)][lampe-experte]
* [Bewegungsmelder][bewegungsmelder]
* [Fenster (Position)][fenster-position]
* [Fenster (Hoch/Runter)][fenster-hoch-runter]
* [Feuchtigkeitssensor][feuchtigkeitssensor]
* [Lüfter][luefter]
* [Garagentor][garagentor]
* [Helligkeitssensor][helligkeitssensor]
* [Kontaktsensor][kontaktsensor]
* [Kohlendioxid Sensor][kohlendioxid-sensor]
* [Kohlenmonoxid Sensor][kohlenmonoxid-sensor]
* [Lautsprecher][lautsprecher]
* [Leckagesensor][leckagesensor]
* [Luftgütesensor][luftguetesensor]
* [Rauchmelder][rauchmelder]
* [Rollladen/Jalousie (Position)][rollladen-jalousie-position]
* [Rollladen/Jalousie (Hoch/Runter)][rollladen-jalousie-hoch-runter]
* [Schloss][schloss]
* [Sicherheitssystem][sicherheitssystem]
* [Temperatursensor][temperatursensor]
* [Thermostat][thermostat]
* [Zustandsloser programmierbarer Schalter][zustandsloser-programmierbarer-schalter]
* [Zwischenstecker][zwischenstecker]

#### Maximale Anzahl an Geräten

HomeKit erlaubt pro Bridge maximal 150 Geräte, wobei die Bridge selbst mitzählt. In Symcon können daher pro Bridge bis zu 149 Geräte eingerichtet werden. Werden mehr Geräte eingerichtet, so erscheint über der Liste ein Hinweis und die überzähligen Geräte erhalten den Status "Limit überschritten". Sie sind in HomeKit nicht verfügbar. Dabei bleiben immer die Geräte mit den kleinsten IDs erhalten, sodass neu hinzugefügte Geräte keine bestehenden verdrängen.

Werden mehr Geräte benötigt, kann eine weitere HomeKit Bridge Instanz erstellt werden. Dabei müssen Name, ID und Port in den [Expertenoptionen][expertenoptionen] für jede Bridge unterschiedlich sein.

### Symcon mit Apple HomeKit koppeln

Abschließend muss Symcon noch mit Apple HomeKit gekoppelt werden, damit die eingerichteten Geräte in HomeKit verfügbar sind. Dazu in der Instanz auf "Jetzt koppeln" klicken. Es öffnet sich ein Fenster mit einem QR-Code und dem achtstelligen Code für die Einrichtung. Beide sind für maximal 5 Minuten gültig. Danach kann über "Jetzt koppeln" ein neuer Code angefordert werden.

<!-- Screenshot fehlt: Fenster "Mit HomeKit koppeln" mit QR-Code -->

In der Home-App auf "Gerät hinzufügen" bzw. auf das "+" oben rechts tippen.

_Tipp: Wenn Ihr z.B. ein Ferienhaus besitzt, könnt Ihr in der Home-App auch weitere Zuhause hinzufügen._

![Bridge hinzufügen][add-bridge-1]

Am einfachsten wird nun der angezeigte QR-Code mit der Kamera gescannt. Alternativ kann der Code auch von Hand eingegeben werden. Dazu "Weitere Optionen ..." auswählen.

![Bridge mit Code hinzufügen][add-bridge-2]

Im folgenden Dialog sollte bei "In der Nähe" ein Eintrag "Symcon" erscheinen. Diesen anklicken, um mit der Einrichtung fortzufahren.

_Tipp: Erscheint das Gerät nicht, so sollten die Firewall-Einstellungen geprüft werden. Außerdem muss die Einrichtung im selben Netzwerk wie der Symcon Server geschehen. Sofern Docker verwendet wird, muss der Host-Modus genutzt werden._

![Bridge auswählen][add-bridge-3]

Die Abfrage "Nicht zertifiziertes Gerät" muss mit "Trotzdem hinzufügen" bestätigt werden.

![Bridge bestätigen][add-bridge-4]

Wurde der QR-Code nicht gescannt, wird nun der Code aus dem Fenster in Symcon eingegeben.

![Code eingeben][add-bridge-5]

Den Raum der Bridge auswählen und mit "Weiter" fortfahren.

![Raum auswählen][add-bridge-6]

Namen ggf. ändern und mit "Weiter" bestätigen.

![Namen ändern][add-bridge-7]

Mit "Weiter" die Einrichtung der Bridge bestätigen.

![Bridge hinzufügen abgeschlossen][add-bridge-8]

Anschließend werden die Geräte der Bridge nacheinander angezeigt. Für jedes Gerät mit "Weiter" bestätigen, z.B. für ein Licht.

![Licht hinzufügen][add-bridge-9]

Den Raum des Geräts auswählen und mit "Weiter" fortfahren.

![Raum auswählen][add-bridge-10]

Namen ggf. ändern und mit "Weiter" bestätigen.

![Namen ändern][add-bridge-11]

Mit "Fertig" die Einrichtung abschließen.

![Hinzufügen abgeschlossen][add-bridge-12]

In der Home-App sind nun die eingerichteten Geräte sichtbar. Geräte, denen kein Raum zugewiesen wurde, werden im Standardraum angezeigt.

![Geräteübersicht][add-bridge-13]

#### Erneut koppeln

Eine Bridge kann immer nur mit einem Zuhause gekoppelt sein. Ist die Bridge bereits gekoppelt, so heißt der Button "Erneut koppeln". Erneutes Koppeln entfernt die bestehende Kopplung und erstellt eine neue. Die Geräte müssen danach in der Home-App neu eingerichtet werden.

Sollen weitere Personen die Geräte nutzen, so ist kein erneutes Koppeln nötig. Stattdessen werden diese Personen in der Home-App in das Zuhause eingeladen.

### Fehlerbehebung

* __Die Bridge erscheint nicht in der Home-App:__ Der Server Socket der Bridge muss aktiv sein. Außerdem muss das iPhone bzw. iPad im selben Netzwerk sein und die Firewall den Port der Bridge (Standard: TCP 34587) sowie Bonjour/mDNS (UDP 5353) zulassen.
* __Ein Gerät fehlt in HomeKit:__ In der Spalte "Status" der Geräteliste steht, warum das Gerät nicht verfügbar ist.
* __Die Bridge reagiert in der Home-App nicht mehr:__ Ggf. wurde die Kopplung entfernt, z.B. über "Erneut koppeln". In diesem Fall die Bridge in der Home-App entfernen und neu hinzufügen.

### Weitere Themen

* [Expertenoptionen][expertenoptionen]

[add-bridge-1]: ../imgs/add-bridge-1.jpg
[add-bridge-2]: ../imgs/add-bridge-2.jpg
[add-bridge-3]: ../imgs/add-bridge-3.jpg
[add-bridge-4]: ../imgs/add-bridge-4.jpg
[add-bridge-5]: ../imgs/add-bridge-5-ios.jpg
[add-bridge-6]: ../imgs/add-bridge-6.jpg
[add-bridge-7]: ../imgs/add-bridge-7.jpg
[add-bridge-8]: ../imgs/add-bridge-8.jpg
[add-bridge-9]: ../imgs/add-bridge-9.jpg
[add-bridge-10]: ../imgs/add-bridge-10.jpg
[add-bridge-11]: ../imgs/add-bridge-11.jpg
[add-bridge-12]: ../imgs/add-bridge-12.jpg
[add-bridge-13]: ../imgs/add-bridge-13.jpg
[add-instance]: ../imgs/add-instance.png
[create-instance-1]: ../imgs/create-instance-1.png
[module]: ../imgs/module.png

[modulecontrol]: https://www.symcon.de/service/dokumentation/modulreferenz/module-control/
[instanzen]: https://www.symcon.de/service/dokumentation/konzepte/instanzen/

[lampe-schaltbar]: types/lampe-schaltbar.md
[lampe-dimmbar]: types/lampe-dimmbar.md
[lampe-farbig]: types/lampe-farbig.md
[lampe-experte]: types/lampe-experte.md
[bewegungsmelder]: types/bewegungsmelder.md
[fenster-position]: types/fenster-position.md
[fenster-hoch-runter]: types/fenster-hoch-runter.md
[feuchtigkeitssensor]: types/feuchtigkeitssensor.md
[luefter]: types/luefter.md
[garagentor]: types/garagentor.md
[helligkeitssensor]: types/helligkeitssensor.md
[kontaktsensor]: types/kontaktsensor.md
[kohlendioxid-sensor]: types/kohlendioxid-sensor.md
[kohlenmonoxid-sensor]: types/kohlenmonoxid-sensor.md
[lautsprecher]: types/lautsprecher.md
[leckagesensor]: types/leckagesensor.md
[luftguetesensor]: types/luftguetesensor.md
[rauchmelder]: types/rauchmelder.md
[rollladen-jalousie-position]: types/rollladen-jalousie-position.md
[rollladen-jalousie-hoch-runter]: types/rollladen-jalousie-hoch-runter.md
[schloss]: types/schloss.md
[sicherheitssystem]: types/sicherheitssystem.md
[temperatursensor]: types/temperatursensor.md
[thermostat]: types/thermostat.md
[zustandsloser-programmierbarer-schalter]: types/zustandsloser-programmierbarer-schalter.md
[zwischenstecker]: types/zwischenstecker.md
[expertenoptionen]: types/expertenoptionen.md
