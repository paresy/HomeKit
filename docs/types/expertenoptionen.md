_Die Expertenoptionen müssen üblicherweise nicht angepasst werden. Gehen Sie mit diesen Optionen sorgsam um, damit das Apple HomeKit Modul wie vorgesehen funktioniert._

### Optionen

Name     | Beschreibung
-------- | ---------------
Name     | Eindeutiger Name der Bridge. Keine Leer- oder Sonderzeichen erlaubt.
ID       | Eindeutige ID der Bridge im Format eine MAC-Adresse.
Port     | Port, über den die HomeKit Bridge kommuniziert.

Sollte eine der Optionen verändert werden, muss die Bridge neu eingelernt werden. Sollten mehrere Bridges in einem IP-Symcon verwendet werden, so müssen alle drei Werte für jede Bridge anders/eindeutig sein. Nach Änderung des Ports muss der I/O der HomeKit Bridge Instanz angepasst werden, sodass er auf dem neuen Port arbeitet. Nach Änderung von Name/ID muss unter Kern-Instanzen in der DNS-SD Instanz manuell der alte Eintrag entfernt werden. 