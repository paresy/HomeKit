### Beschreibung

Geräte vom Typ Thermostat können Soll-Temperaturen einstellen und Ist-Temperaturen anzeigen.

### Parameter

Name             | Beschreibung
---------------- | ---------------
Name             | Name mit dem das Gerät über HomeKit angesprochen werden kann
Soll-Variable    | Eine schaltbare Variable vom Typ Float, durch welche die Temperatur eingestellt wird
Ist-Variable     | Eine Variable vom Typ Float, durch welche die Ist-Temperatur angezeigt wird
Zustand-Variable | Eine schaltbare Variable vom Typ Integer, welche den aktuellen Zustand anzeigt. (0: Aus, 1: Heizen, 2: Kühlen, 3: Automatisch)


#### Mögliche Aktionen

Aktion                 | Beschreibung                                                                  | Möglicher Satz zum Aktivieren
---------------------- | ----------------------------------------------------------------------------- | -----------------------------
Temperatur einstellen  | Schalte die Soll-Variable auf die angegebene Temperatur.                      | "Hey Siri, stelle _<Name\>_ auf 35 °C."
Temperatur abfragen    | Fragt die eingestelle Temperatur oder Soll-Variable ab.                       | "Hey Siri, wie ist die Temperatur von _<Name\>_."