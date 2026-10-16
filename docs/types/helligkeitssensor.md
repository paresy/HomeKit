### Beschreibung

Geräte vom Typ Helligkeitssensor übertragen die Helligkeit an HomeKit.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine Variable vom Typ Integer oder Float, welche die Helligkeit in Lux (0,0001-100.000) enthält. Eine Aktion wird nicht benötigt

#### Mögliche Aktionen

Aktion              | Beschreibung                     | Möglicher Satz zum Aktivieren
------------------- | -------------------------------- | -----------------------------
Helligkeit abfragen | Fragt die aktuelle Helligkeit ab | "Hey Siri, wie ist die Helligkeit von _<Name\>_?"
