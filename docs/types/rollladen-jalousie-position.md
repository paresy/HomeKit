### Beschreibung

Geräte vom Typ Rollladen/Jalousie (Position) beschreiben Rollladen/Jalousien, welche positioniert werden können.

### Parameter

Name       | Beschreibung
---------- | ---------------
Name       | Name mit dem das Gerät über HomeKit angesprochen werden kann
Variable   | Eine schaltbare Variable vom Typ Integer oder Float, über welche der/die Rollladen/Jalousie positioniert wird

#### Mögliche Aktionen

Aktion               | Beschreibung                                   | Möglicher Satz zum Aktivieren
-------------------- | ---------------------------------------------- | -----------------------------
Öffnen               | Schaltet die Variable auf 100%                 | "Hey Siri, öffne _<Name\>_ an."
Schließen            | Schaltet die Variable auf 0%                   | "Hey Siri, öffne _<Name\>_ an."
Positionieren        | Schaltet die Variable auf den angegebenen Wert | "Hey Siri, fahre _<Name\>_ auf 40%."