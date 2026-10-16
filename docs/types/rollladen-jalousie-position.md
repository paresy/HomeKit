### Beschreibung

Geräte vom Typ Rollladen/Jalousie (Position) beschreiben Rollläden bzw. Jalousien, welche positioniert werden können.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine [dimmbare Variable](../README.md#anforderungen-an-variablen) mit Aktion, über welche der Rollladen bzw. die Jalousie positioniert wird. Variablen mit der Darstellung "Rollladen" werden ebenfalls unterstützt

HomeKit gibt an, wie weit geöffnet wird, die Variable dagegen, wie weit geschlossen wird. Der Wert wird daher umgekehrt: 0 % der Variable entspricht in HomeKit einer vollständig geöffneten Position (100 %), 100 % der Variable einer geschlossenen. Das entspricht der üblichen Darstellung von Rollläden in Symcon (0 % = oben).

#### Mögliche Aktionen

Aktion        | Beschreibung                                                                | Möglicher Satz zum Aktivieren
------------- | --------------------------------------------------------------------------- | -----------------------------
Öffnen        | Schaltet die Variable auf 0 %                                               | "Hey Siri, öffne _<Name\>_."
Schließen     | Schaltet die Variable auf 100 %                                             | "Hey Siri, schließe _<Name\>_."
Positionieren | Öffnet auf den angegebenen Wert, z.B. schaltet "40 %" die Variable auf 60 % | "Hey Siri, öffne _<Name\>_ auf 40%."
