### Beschreibung

Geräte vom Typ Sicherheitssystem beschreiben den Zustand einer Alarmanlage und können diese scharf bzw. unscharf schalten.

### Parameter

Feld               | Beschreibung
------------------ | ------------
Name               | Name, unter dem das Gerät in HomeKit erscheint
VariablenID        | Eine Variable vom Typ Integer mit Aktion, welche den Zustand der Alarmanlage enthält. Sie benötigt das Profil SecuritySystem.HomeKit, welches die Bridge automatisch anlegt, oder die Darstellung "Aufzählung" mit den unten genannten Werten. Andernfalls lautet der Status "Falsches Profil"
AlarmID (Optional) | Eine Variable vom Typ Boolean. Ist sie True, zeigt HomeKit einen ausgelösten Alarm an

### Werte

Wert | Bedeutung
---- | ---------
0    | Zuhause
1    | Abwesend
2    | Nacht
3    | Aus

#### Mögliche Aktionen

Aktion                  | Beschreibung                                      | Möglicher Satz zum Aktivieren
----------------------- | ------------------------------------------------- | -----------------------------
Alarmanlage schalten    | Schaltet die Variable auf den angegebenen Zustand | "Hey Siri, schalte _<Name\>_ auf Abwesend."
Alarmanlage ausschalten | Schaltet die Variable auf 3 (Aus)                 | "Hey Siri, schalte _<Name\>_ aus."
