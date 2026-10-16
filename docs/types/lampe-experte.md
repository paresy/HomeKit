### Beschreibung

Geräte vom Typ Lampe (Experte) beschreiben Lampen, bei denen Zustand und Helligkeit über zwei getrennte Variablen geschaltet werden.

### Parameter

Feld          | Beschreibung
------------- | ------------
Name          | Name, unter dem das Gerät in HomeKit erscheint
StatusID      | Eine [Schaltvariable](../README.md#anforderungen-an-variablen) mit Aktion, über welche die Lampe an- und ausgeschaltet wird
HelligkeitsID | Eine [dimmbare Variable](../README.md#anforderungen-an-variablen) mit Aktion, über welche die Lampe gedimmt wird

#### Mögliche Aktionen

Aktion               | Beschreibung                                               | Möglicher Satz zum Aktivieren
-------------------- | ---------------------------------------------------------- | -----------------------------
An oder Aus schalten | Schaltet die Status-Variable auf True oder False           | "Hey Siri, schalte _<Name\>_ an."
Dimmen               | Schaltet die Helligkeits-Variable auf den angegebenen Wert | "Hey Siri, dimme _<Name\>_ auf 40%."
