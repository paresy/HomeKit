### Beschreibung

Geräte vom Typ Thermostat können Soll-Temperaturen einstellen und Ist-Temperaturen anzeigen.

Ob gerade geheizt oder gekühlt wird, ermittelt die Bridge aus den beiden Temperaturen: Liegt die Ist-Temperatur unter der Soll-Temperatur, wird im Modus "Heizen" oder "Automatisch" geheizt. Liegt sie darüber, wird im Modus "Kühlen" oder "Automatisch" gekühlt.

### Parameter

Feld                                   | Beschreibung
-------------------------------------- | ------------
Name                                   | Name, unter dem das Gerät in HomeKit erscheint
TargetHeatingCoolingStateID (Optional) | Eine Variable vom Typ Integer mit Aktion, welche den Modus enthält. Ohne diese Variable zeigt HomeKit immer den Modus "Automatisch"
CurrentTemperatureID                   | Eine Variable vom Typ Float, welche die Ist-Temperatur in °C enthält
TargetTemperatureID                    | Eine Variable vom Typ Float mit Aktion, über welche die Soll-Temperatur in °C eingestellt wird. HomeKit erlaubt Werte von 10 bis 38 °C in Schritten von 0,1 °C

### Werte des Modus

Wert | Bedeutung
---- | ---------
0    | Aus
1    | Heizen
2    | Kühlen
3    | Automatisch

#### Mögliche Aktionen

Aktion                | Beschreibung                                          | Möglicher Satz zum Aktivieren
--------------------- | ----------------------------------------------------- | -----------------------------
Temperatur einstellen | Schaltet die Soll-Temperatur auf den angegebenen Wert | "Hey Siri, stelle _<Name\>_ auf 21 °C."
Temperatur abfragen   | Fragt die Ist-Temperatur ab                           | "Hey Siri, wie ist die Temperatur von _<Name\>_?"
