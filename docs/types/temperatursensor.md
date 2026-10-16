### Beschreibung

Geräte vom Typ Temperatursensor übertragen die aktuelle Temperatur an HomeKit.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine Variable vom Typ Integer oder Float, welche die Temperatur in °C (-100 bis 100) enthält. Eine Aktion wird nicht benötigt

#### Mögliche Aktionen

Aktion              | Beschreibung                     | Möglicher Satz zum Aktivieren
------------------- | -------------------------------- | -----------------------------
Temperatur abfragen | Fragt die aktuelle Temperatur ab | "Hey Siri, wie ist die Temperatur von _<Name\>_?"
