### Beschreibung

Geräte vom Typ Sicherheitssystem beschreiben den Zustand eines Alarmierungssystems.

### Parameter

Name       | Beschreibung
---------- | ---------------
Name       | Name mit dem das Gerät über HomeKit angesprochen werden kann
Variable   | Eine schaltbare Variable vom Typ Integer, die den Zustand des Alarmierungssystems aufzeigt. Die Variable muss das Profil SecuritySystem.HomeKit gesetzt haben und die Aktion dies korrekt unterstützen.

#### Mögliche Aktionen

Aktion                        | Beschreibung                                      | Möglicher Satz zum Aktivieren
----------------------------- |---------------------------------------------------| -----------------------------
Schalte Alarm auf Aus         | Schaltet die Variable auf 3 für den Zustand "Aus" | "Hey Siri, schalte _<Name\>_ auf Aus."