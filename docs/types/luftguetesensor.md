### Beschreibung

Geräte vom Typ Luftgütesensor übertragen die aktuelle Luftqualität an HomeKit.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine Variable vom Typ Integer, welche den aktuellen Wert der Luftgüte enthält. Eine Aktion wird nicht benötigt

### Werte

Wert | Bedeutung
---- | ---------
0    | Unbekannt
1    | Hervorragend
2    | Gut
3    | Ok
4    | Minderwertig
5    | Schlecht

#### Mögliche Aktionen

Aktion                | Beschreibung                       | Möglicher Satz zum Aktivieren
--------------------- | ---------------------------------- | -----------------------------
Luftqualität abfragen | Fragt die aktuelle Luftqualität ab | "Hey Siri, wie ist die Luftqualität von _<Name\>_?"
