### Beschreibung

Geräte vom Typ Lampe (Dimmbar) beschreiben Lampen, welche gedimmt werden können. Die Lampe gilt als an, sobald die Helligkeit größer als 0 % ist.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine [dimmbare Variable](../README.md#anforderungen-an-variablen) mit Aktion, über welche die Lampe gedimmt wird

#### Mögliche Aktionen

Aktion       | Beschreibung                                                                                                        | Möglicher Satz zum Aktivieren
------------ | ------------------------------------------------------------------------------------------------------------------- | -----------------------------
An schalten  | Schaltet die Variable auf 100 %, sofern die Lampe aus ist. Eine bereits eingeschaltete Lampe behält ihre Helligkeit | "Hey Siri, schalte _<Name\>_ an."
Aus schalten | Schaltet die Variable auf 0 %                                                                                       | "Hey Siri, schalte _<Name\>_ aus."
Dimmen       | Schaltet die Variable auf den angegebenen Wert                                                                      | "Hey Siri, dimme _<Name\>_ auf 40%."
