_Die Expertenoptionen müssen üblicherweise nicht angepasst werden. Gehen Sie mit diesen Optionen sorgsam um, damit das Apple HomeKit Modul wie vorgesehen funktioniert._

Die Expertenoptionen werden über den Button "Expertenoptionen" unten in der Instanz geöffnet.

### Optionen

Name | Beschreibung
---- | ------------
Name | Name der Bridge, unter dem sie in der Home-App gefunden wird. Nur Buchstaben und Zahlen sind erlaubt.
ID   | Eindeutige ID der Bridge im Format einer MAC-Adresse, z.B. 1A:2B:3C:4D:5E:6F.
Port | Port, über den die Home-App mit der Bridge kommuniziert (Standard: 34587). Die Bridge gibt diesen Port ihrem Server Socket vor, er muss dort nicht angepasst werden.

Wird die ID verändert, erkennt die Home-App die Bridge als neues Gerät. Sie muss dann erneut gekoppelt und die Geräte müssen neu eingerichtet werden.

Nach Änderung des Namens oder der ID muss unter Kern Instanzen in der DNS-SD Instanz der alte Eintrag manuell entfernt werden.

### Mehrere Bridges

Werden mehrere Bridges in einem Symcon verwendet, z.B. weil mehr als 149 Geräte benötigt werden, so müssen Name, ID und Port für jede Bridge unterschiedlich sein. Jede Bridge benötigt außerdem einen eigenen Server Socket.
