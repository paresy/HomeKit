### Beschreibung

Geräte vom Typ Lüfter (Regulierbar) beschreiben Lüfter, welche reguliert werden können.

### Parameter

Name       | Beschreibung
---------- | ---------------
Name       | Name mit dem das Gerät über HomeKit angesprochen werden kann
Variable   | Eine schaltbare Variable vom Typ Integer oder Float, über welche der Lüfter gesteuert wird

#### Mögliche Aktionen

Aktion               | Beschreibung                                   | Möglicher Satz zum Aktivieren
-------------------- | ---------------------------------------------- | -----------------------------
An oder Aus schalten | Schaltet die Variable auf 100% oder 0%         | "Hey Siri, schalte _<Name\>_ an."
Regulieren           | Schaltet die Variable auf den angegebenen Wert | "Hey Siri, dimme _<Name\>_ auf 40%."