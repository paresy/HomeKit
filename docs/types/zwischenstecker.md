### Beschreibung

Geräte vom Typ Zwischenstecker beschreiben Geräte, welche an- und ausgeschaltet werden können. In der Home-App erscheinen sie als Schalter.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine [Schaltvariable](../README.md#anforderungen-an-variablen) mit Aktion, über welche das Gerät an- und ausgeschaltet wird

#### Mögliche Aktionen

Aktion               | Beschreibung                              | Möglicher Satz zum Aktivieren
-------------------- | ----------------------------------------- | -----------------------------
An oder Aus schalten | Schaltet die Variable auf True oder False | "Hey Siri, schalte _<Name\>_ an."
