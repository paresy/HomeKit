### Beschreibung

Geräte vom Typ Rollladen/Jalousie (Hoch/Runter) beschreiben Rollläden bzw. Jalousien, die geöffnet bzw. geschlossen werden können.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine [Rollladenvariable](../README.md#anforderungen-an-variablen) mit Aktion, über welche der Rollladen bzw. die Jalousie geöffnet bzw. geschlossen wird

### Werte

Wert | Bedeutung
---- | ---------
0    | Offen/Hoch (in HomeKit 100 % geöffnet)
2    | Stopp (in HomeKit 50 % geöffnet)
4    | Geschlossen/Runter (in HomeKit 0 % geöffnet)

Da die Variable keine Zwischenpositionen kennt, öffnet jede Position über 0 % vollständig. Mit dem Stopp-Button in der Home-App wird die Variable auf 2 geschaltet.

#### Mögliche Aktionen

Aktion                | Beschreibung                                       | Möglicher Satz zum Aktivieren
--------------------- | -------------------------------------------------- | -----------------------------
Öffnen oder Schließen | Schaltet die Variable auf 0 (Hoch) oder 4 (Runter) | "Hey Siri, öffne _<Name\>_."
