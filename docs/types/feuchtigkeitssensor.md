### Beschreibung

Geräte vom Typ Feuchtigkeitssensor übertragen die relative Luftfeuchtigkeit an HomeKit.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine Variable vom Typ Integer oder Float, welche die relative Luftfeuchtigkeit in Prozent (0-100) enthält. Eine Aktion wird nicht benötigt

#### Mögliche Aktionen

Aktion                    | Beschreibung                           | Möglicher Satz zum Aktivieren
------------------------- | -------------------------------------- | -----------------------------
Luftfeuchtigkeit abfragen | Fragt die aktuelle Luftfeuchtigkeit ab | "Hey Siri, wie ist die Luftfeuchtigkeit von _<Name\>_?"
