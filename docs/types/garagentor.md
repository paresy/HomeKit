### Beschreibung

Geräte vom Typ Garagentor beschreiben Tore, welche geöffnet bzw. geschlossen werden können.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine [Rollladenvariable](../README.md#anforderungen-an-variablen) mit Aktion, über welche das Tor geöffnet bzw. geschlossen wird

### Werte

Wert | Bedeutung
---- | ---------
0    | Offen
2    | Gestoppt (wird in HomeKit als "Gestoppt" angezeigt)
4    | Geschlossen

Ein Hindernis meldet die Bridge nie an HomeKit.

#### Mögliche Aktionen

Aktion                | Beschreibung                                             | Möglicher Satz zum Aktivieren
--------------------- | -------------------------------------------------------- | -----------------------------
Öffnen oder Schließen | Schaltet die Variable auf 0 (Offen) oder 4 (Geschlossen) | "Hey Siri, öffne _<Name\>_."
