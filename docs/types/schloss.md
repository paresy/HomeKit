### Beschreibung

Geräte vom Typ Schloss beschreiben Schlösser, welche ver- und entriegelt werden können.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine [Schaltvariable](../README.md#anforderungen-an-variablen) mit Aktion, über welche das Schloss geschaltet wird (True = verriegelt, False = entriegelt)

#### Mögliche Aktionen

Aktion     | Beschreibung                    | Möglicher Satz zum Aktivieren
---------- | ------------------------------- | -----------------------------
Verriegeln | Schaltet die Variable auf True  | "Hey Siri, verriegle _<Name\>_."
Entriegeln | Schaltet die Variable auf False | "Hey Siri, entriegle _<Name\>_."
