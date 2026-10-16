### Beschreibung

Geräte vom Typ Lampe (Farbig) beschreiben Lampen, welche beliebige Farben annehmen können.

Ohne HelligkeitsID ist die Helligkeit Teil der Farbe: Dimmen verändert die Helligkeit der aktuellen Farbe und "Aus" setzt die Farbe auf Schwarz. Mit HelligkeitsID wird die Helligkeit stattdessen über diese Variable geschaltet und die Farbvariable enthält nur noch die Farbe. Die Lampe gilt als an, sobald die Helligkeit größer als 0 % ist.

### Parameter

Feld          | Beschreibung
------------- | ------------
Name          | Name, unter dem das Gerät in HomeKit erscheint
VariablenID   | Eine Farbvariable mit Aktion: eine Variable vom Typ Integer mit dem Profil ~HexColor oder eine Variable vom Typ Integer oder String mit der Darstellung "Farbe". Bei String-Variablen werden die Kodierungen RGB, CMYK, HSV, HSL und xy unterstützt
HelligkeitsID | Optional. Eine [dimmbare Variable](../README.md#anforderungen-an-variablen) mit Aktion, über welche die Helligkeit geschaltet wird. Wird für Farbvariablen mit der Kodierung xy benötigt, da diese keine Helligkeit enthält. Fehlt sie dort, lautet der Status "Separate Helligkeit für xy-Kodierung benötigt"

#### Mögliche Aktionen

Aktion         | Beschreibung                                                                                                          | Möglicher Satz zum Aktivieren
-------------- | --------------------------------------------------------------------------------------------------------------------- | -----------------------------
An schalten    | Schaltet die Helligkeit auf 100 %, sofern die Lampe aus ist. Eine bereits eingeschaltete Lampe behält ihre Helligkeit | "Hey Siri, schalte _<Name\>_ an."
Aus schalten   | Schaltet die Helligkeit auf 0 %                                                                                       | "Hey Siri, schalte _<Name\>_ aus."
Dimmen         | Schaltet die Helligkeit auf den angegebenen Wert                                                                      | "Hey Siri, dimme _<Name\>_ auf 40%."
Farbe schalten | Schaltet die Farbvariable auf die angegebene Farbe                                                                    | "Hey Siri, schalte _<Name\>_ auf rot."
