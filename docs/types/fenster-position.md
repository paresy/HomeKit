### Beschreibung

Geräte vom Typ Fenster (Position) beschreiben Fenster, welche positioniert werden können.

### Parameter

Name       | Beschreibung
---------- | ---------------
Name       | Name mit dem das Gerät über HomeKit angesprochen werden kann
Variable   | Eine schaltbare Variable vom Typ Integer oder Float, über welche das Fenster positioniert wird

#### Mögliche Aktionen

Aktion               | Beschreibung                                   | Möglicher Satz zum Aktivieren
-------------------- | ---------------------------------------------- | -----------------------------
Öffnen               | Schaltet die Variable auf 0%                   | "Hey Siri, öffne _<Name\>_."
Schließen            | Schaltet die Variable auf 100%                 | "Hey Siri, schließe _<Name\>_."
Positionieren        | Schaltet die Variable auf den angegebenen Wert. Dabei wird der Wert invertiert, sodass der eigentliche Wert von 100 abgezogen wird | "Hey Siri, fahre _<Name\>_ auf 40%."
