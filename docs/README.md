<!-- Screenshots mit 1300px Breite erstellt -->

###Beschreibung

Das Apple HomeKit-Modul ermöglicht die Steuerung von Geräten durch Apple Siri. Dies beinhaltet insbesondere die Sprachsteuerung über HomePods, iPads und iPhones.

###Einbindung in IP-Symcon

_Um HomeKit über das Internet außerhalb des eigenen WLANs verwenden zu können ist ein dauerhaft eingeschalteter HomePod oder AppleTV notwendig. Theoretisch kann auch ein dauerhaft eingeschaltetes iPad dazu verwendet werden - dies scheint jedoch aktuell eher zu Problemen zu führen und wird nicht empfohlen._

<!--
<p>
    <iframe width="680" height="382" class="videoblock" id="tutorial-einrichten" frameborder="0"
            src="https://www.youtube.com/embed/xxxxxxx?rel=0&cc_load_policy=1">
    </iframe>
</p>
-->

#### Apple HomeKit-Instanz erstellen

Damit das Apple HomeKit-Modul verwendet werden kann, muss es zuerst über das [Module Control][modulecontrol] installiert werden. Hierfür muss das [Module Control][modulecontrol] geöffnet werden. Dies befindet sich unter der Hauptkategorie "Kern Instanzen" im Objektbaum oder kann direkt über das Widget "Aktualisierungen" geöffnet werden.
Zur Installation im [Module Control][modulecontrol] den Hinzufügen-Button unten rechts betätigen. Im sich öffnenden Dialog die URL https://github.com/paresy/HomeKit eingeben und mit "OK" bestätigen.

![Modul installieren][module]

Als nächstes eine [Instanz][instanzen] vom Apple HomeKit-Modul erstellen. Zuerst den Objektbaum öffnen. Hier den Hinzufügen-Button unten rechts betätigen und [Instanz][instanzen] auswählen.

![Instanz hinzufügen][add-instance-1]

In der Modulliste das Gerät "HomeKit Bridge" vom Hersteller "Apple" auswählen. Der Ort sollte nicht verändert werden, der Name kann beliebig gewählt werden. Abschließend mit "OK" bestätigen.

![Instanz konfigurieren][add-instance-2]

Im folgenden Dialog muss der Schalter auf Aktiv umgestellt und ebenfalls mit "OK" bestätigt werden.

![Instanz erstellen][create-instance]

Nach der Erstellung der Apple HomeKit-Instanz öffnet sich diese automatisch und kann eingerichtet werden.

#### Apple HomeKit-Instanz einrichten
Das Modul bietet verschiedene Gerätetypen an, welche verwendet werden können. Um ein entsprechendes Gerät einzurichten, wird das dazugehörige Panel ausgeklappt und in der entsprechenden Liste durch einen Klick auf "Hinzufügen" ein neuer Eintrag erstellt.

![Gerät hinzufügen][add-device-1]

Jeder Eintrag erfordert einen Namen unter welchem das Gerät bei Apple HomeKit bekannt sein wird. Weitere Parameter hängen vom jeweiligen Gerätetyp ab und können den dazugehörigen Seiten der Dokumentation entnommen werden. Nachdem alle Parameter gesetzt sind, wird das Gerät mit "OK" bestätigt.

![Gerät konfigurieren][add-device-2]

####Mögliche Gerätetypen

* [Licht (Schaltbar)][licht-schalbar]
* [Licht (Dimmbar)][licht-dimmbar]
* [Licht (Farbig)][licht-farbig]
* [Bewegungsmelder][bewegungsmelder]
* [Fenster (Position)][fenster-position]
* [Fenster (Hoch/Runter)][fenster-hochrunter]
* [Feuchtigskeitssensor][feuchtigkeitssensor]
* [Garagentor][garagentor]
* [Helligkeitssensor][helligkeitssensor]
* [Kontaktsensor][kontaktsensor]
* [Kohlendioxidsensor][kohlendioxidsensor]
* [Kohlenmonoxidsensor][kohlenmonoxidsensor]
* [Lautsprecher][lautsprecher]
* [Leckagesensor][leckagesensor]
* [Luftgütesensor][luftguetesensor]
* [Rauchmelder][rauchmelder]
* [Rollladen (Position)][rollladen-position]
* [Rollladen (Hoch/Runter)][rollladen-hochrunter]
* [Temperatursensor][temperatursensor]
* [Thermostat][thermostat]
* [Zwischenstecker][zwischenstecker]

Nachdem alle Geräte eingerichtet sind, werden diese mit einem Klick auf "Änderungen übernehmen" bestätigt.

![Änderungen übernehmen][apply-changes]

Nach dem Übernehmen sollte sichergestellt werden, dass bei allen Geräten "OK" in der Spalte "Status" steht. Ist dies nicht der Fall, so enthält die Spalte eine Fehlermeldung, welche über die Komplikation informiert.

![Status prüfen][check]


#### IP-Symcon mit Apple HomeKit verknüpfen ####
Abschließend muss IP-Symcon noch mit Apple HomeKit verknüpft werden, damit die eingerichteten Geräte in in HomeKit verfügbar sind. Hierfür muss IP-Symcon als HomeKit Bridge in der Home-App hinzugefügt werden. Dazu in der Home-App den "Hinzufügen" Button drücken.

_Tipp: Wenn Ihr z.B. ein Ferienhaus besitzt, könnte Ihr in der Home-App auch weitere Häuser hinzufügen_

![Bridge hinzufügen][add-bridge-1]

Bitte "Code fehlt bzw. kann nicht gescannt werden?" auswählen.

![Bridge mit Code hinzufügen][add-bridge-2]

Im folgenden Dialog sollte bei "Geräte in der Nähe" ein Eintrag "Symcon" erscheinen. Diesen anklicken, um mit der Einrichtung fortzufahren.

_Tipp: Erscheint das Gerät nicht so sollten die Firewall Einstellung geprüft werden. Außerdem muss die Einrichtung im selben WLAN wie der IP-Symcon Server geschehen. Sofern Docker verwendet wird muss der Host-Modus genutzt werden. Aktuell scheint es insbesondere auf Synology NAS Probleme bei der Einrichtung zu geben._ 

![Bridge auswählen][add-bridge-3]

Die Abfrage "Nicht zertifiziertes Gerät" muss mit "Trotzdem hinzufügen" bestätigt werden. 

![Bridge bestätigen][add-bridge-4]

... Code anfordern! ... -> Eintippen in der Home-App.

![Code eingeben][add-bridge-5]

Dieser wird kurz mit "Symcon hinzufügt" bestätigt.
  
![Bridge hinzugefügt][add-bridge-6]

![Geräte bestätigen][add-bridge-7]

![Hinzufügen abgeschlosse][add-bridge-8]

![Geräteübersicht][add-bridge-9]

[add-bridge-1]: ../imgs/add-bridge-1.jpg
[add-bridge-2]: ../imgs/add-bridge-2.jpg
[add-bridge-3]: ../imgs/add-bridge-3.jpg
[add-bridge-4]: ../imgs/add-bridge-4.jpg
[add-bridge-5]: ../imgs/add-bridge-5.png
[add-bridge-6]: ../imgs/add-bridge-6.jpg
[add-bridge-7]: ../imgs/add-bridge-7.jpg
[add-bridge-7]: ../imgs/add-bridge-8.jpg
[add-bridge-7]: ../imgs/add-bridge-9.jpg
[add-device-1]: ../imgs/add-device-1.png
[add-device-2]: ../imgs/add-device-2.png
[add-instance-1]: ../imgs/add-instance-1.png
[add-instance-2]: ../imgs/add-instance-2.png
[apply-changes]: ../imgs/apply-changes.png
[check]: ../imgs/check.png
[create-instance]: ../imgs/create-instance.png
[module]: ../imgs/homekit-module.png

[modulecontrol]: https://www.symcon.de/service/dokumentation/modulreferenz/module-control/
[instanzen]: https://www.symcon.de/service/dokumentation/konzepte/instanzen/

[licht-schalbar]: types/licht-schaltbar/
[licht-dimmbar]: types/licht-dimmbar/
[licht-farbig]: types/licht-farbig/
[lautsprecher]: types/lautsprecher/
[temperatursensor]: types/temperatursensor/
[thermostat]: types/thermostat/