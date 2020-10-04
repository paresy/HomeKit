### Beschreibung

Geräte vom Typ Lampe (Experte) beschreiben Lampen, welche gedimmt werden können, bei der Lampe Expert kann allerdings eine weitere Variable (Status) angegbene werden.

### Parameter

Name       | Beschreibung
---------- | ---------------
Name       | Name mit dem das Gerät über HomeKit angesprochen werden kann
Status Variable   | Eine schaltbare Variable vom Typ Boolean, über welche das Licht ein- oder ausgeschaltet wird
Helligkeits Variable   | Eine schaltbare Variable vom Typ Integer oder Float, über welche das Licht gedimmt wird

#### Mögliche Aktionen

Aktion               | Beschreibung                                   | Möglicher Satz zum Aktivieren
-------------------- | ---------------------------------------------- | -----------------------------
An oder Aus schalten | Schaltet die Variable auf den eingestellten Wert         | "Hey Siri, schalte _<Name\>_ an."
Dimmen               | Schaltet die Variable auf den angegebenen Wert | "Hey Siri, dimme _<Name\>_ auf 40%."