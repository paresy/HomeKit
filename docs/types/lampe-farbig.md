### Beschreibung

Geräte vom Typ Lampe (Farbig) beschreiben Lampen, welche beliebige Farben annehmen können.

### Parameter

Name       | Beschreibung
---------- | ---------------
Name       | Name mit dem das Gerät über HomeKit angesprochen werden kann
Variable   | Eine schaltbare Variable vom Typ Integer mit dem Profil ~HexColor, über welche die Farbe des Lichts geschaltet wird

#### Mögliche Aktionen

Aktion               | Beschreibung                                                                  | Möglicher Satz zum Aktivieren
-------------------- | ----------------------------------------------------------------------------- | -----------------------------
An oder Aus schalten | Schaltet die Variable auf Weiß oder Schwarz                                   | "Hey Siri, schalte _<Name\>_ an."
Dimmen               | Schaltet Helligkeit der aktuellen Farbe der Variable auf den angegebenen Wert | "Hey Siri, dimme _<Name\>_ auf 40%."
Farbe schalten       | Schaltet die Variable auf die angegebene Farbe                                | "Hey Siri, schalte _<Name\>_ auf rot."