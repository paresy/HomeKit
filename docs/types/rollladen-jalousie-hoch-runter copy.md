### Beschreibung

Geräte vom Typ Rollladen/Jalousie (Hoch/Runter) beschreiben Geräte, die einen/eine Rollladen/Jalousie öffen bzw. schließen können.

### Parameter

Name       | Beschreibung
---------- | ---------------
Name       | Name mit dem das Gerät über HomeKit angesprochen werden kann
Variable   | Eine schaltbare Variable vom Typ Integer, über welche Rollladen/Jalousien geöffnet bzw. geschlossen wird. Die Variable muss das Profil ~ShutterMoveStop oder ~ShutterMoveStep gesetzt haben und die Aktion dies korrekt unterstützen.

#### Mögliche Aktionen

Aktion                        | Beschreibung                              | Möglicher Satz zum Aktivieren
----------------------------- | ----------------------------------------- | -----------------------------
Rollladen/Jalousie Öffnen oder Schließen | Schaltet die Variable auf hoch (0) oder runter (4) | "Hey Siri, öffne Rolladen _<Name\>_."