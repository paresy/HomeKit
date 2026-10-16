### Beschreibung

Geräte vom Typ Lampe (Schaltbar) beschreiben Lampen, welche an- und ausgeschaltet werden können.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine [Schaltvariable](../README.md#anforderungen-an-variablen) mit Aktion, über welche die Lampe an- und ausgeschaltet wird

#### Mögliche Aktionen

Aktion               | Beschreibung                              | Möglicher Satz zum Aktivieren
-------------------- | ----------------------------------------- | -----------------------------
An oder Aus schalten | Schaltet die Variable auf True oder False | "Hey Siri, schalte _<Name\>_ an."
