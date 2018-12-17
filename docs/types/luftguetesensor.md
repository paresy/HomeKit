### Beschreibung

Geräte vom Typ Luftgütesensor beschreiben Geräte, welche über die aktuelle Luftqualität benachrichtigen.

### Parameter

Name       | Beschreibung
---------- | ---------------
Name       | Name mit dem das Gerät über HomeKit angesprochen werden kann
Variable   | Eine Variable vom Typ Integer, welche den aktuellen Luftgüte Wert beinhaltet.

### Werte
Wert       | Beschreibung
---------- | ---------------
0          | Unbekannt
1          | Hervorragend
2          | Gut
3          | Ok
4          | Minderwertig
5          | Schlecht

#### Mögliche Aktionen

Aktion               | Beschreibung                              | Möglicher Satz zum Aktivieren
-------------------- | ----------------------------------------- | -----------------------------
Luftqualität abfragen | Fragt die aktuelle Qualität der Luft in einem Raum ab | "Hey Siri, wie ist die Luftqualität von _<Name\>_."