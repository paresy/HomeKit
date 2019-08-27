### Beschreibung

Geräte vom Typ Fenster (Hoch/Runter) beschreiben Geräte, die ein Fenster öffen bzw. schließen können.

### Parameter

Name       | Beschreibung
---------- | ---------------
Name       | Name mit dem das Gerät über HomeKit angesprochen werden kann
Variable   | Eine schaltbare Variable vom Typ Integer, über welche das Fenster geöffnet bzw. geschlossen wird. Die Variable muss das Profil ~ShutterMoveStop oder ~ShutterMoveStep gesetzt haben und die Aktion dies korrekt unterstützen.

#### Mögliche Aktionen

Aktion                        | Beschreibung                              | Möglicher Satz zum Aktivieren
----------------------------- | ----------------------------------------- | -----------------------------
Fenster Öffnen oder Schließen | Schaltet die Variable auf hoch (0) oder runter (4) | "Hey Siri, öffne Fenster _<Name\>_."