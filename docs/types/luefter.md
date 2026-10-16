### Beschreibung

Geräte vom Typ Lüfter beschreiben Lüfter, deren Geschwindigkeit reguliert werden kann. Der Lüfter gilt als an, sobald die Geschwindigkeit größer als 0 % ist.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine [dimmbare Variable](../README.md#anforderungen-an-variablen) mit Aktion, über welche die Geschwindigkeit des Lüfters gesteuert wird

#### Mögliche Aktionen

Aktion       | Beschreibung                                               | Möglicher Satz zum Aktivieren
------------ | ---------------------------------------------------------- | -----------------------------
An schalten  | Schaltet die Variable auf 100 %, sofern der Lüfter aus ist | "Hey Siri, schalte _<Name\>_ an."
Aus schalten | Schaltet die Variable auf 0 %                              | "Hey Siri, schalte _<Name\>_ aus."
Regulieren   | Schaltet die Variable auf den angegebenen Wert             | "Hey Siri, stelle _<Name\>_ auf 40%."
