### Beschreibung

Mit einem Gerät vom Typ Zustandsloser programmierbarer Schalter können aus Symcon heraus Aktionen in HomeKit ausgelöst werden.

In der Home-App erscheint das Gerät als Taster ohne eigenen Zustand. Dort wird für jede Art von Tastendruck festgelegt, was passieren soll, z.B. eine Szene aktivieren oder Geräte schalten, die nur in HomeKit eingebunden sind. Setzt Symcon die Variable auf einen der unten genannten Werte, meldet die Bridge den passenden Tastendruck an HomeKit und HomeKit führt die dafür hinterlegte Aktion aus. Die Variable kann z.B. von einem Taster einer Geräte-Instanz oder von einem Skript gesetzt werden.

### Parameter

Feld        | Beschreibung
----------- | ------------
Name        | Name, unter dem das Gerät in HomeKit erscheint
VariablenID | Eine Variable vom Typ Integer, deren Wert den Tastendruck angibt. Eine Aktion wird nicht benötigt, da HomeKit die Variable nie schaltet

### Werte

Wert | Bedeutung
---- | ---------
0    | Einmal drücken
1    | Zweimal drücken
2    | Lange drücken

### Einrichtung in HomeKit

In der Home-App das Gerät öffnen. Für "Einmal drücken", "Zweimal drücken" und "Lange drücken" kann jeweils ausgewählt werden, welche Szenen oder Geräte geschaltet werden sollen. Da HomeKit diese Aktionen über die Steuerzentrale ausführt, wird ein HomePod oder Apple TV benötigt.

### Beispiel

Folgendes Skript löst die für "Einmal drücken" hinterlegte Aktion aus. Dabei ist 12345 durch die ID der Variable zu ersetzen.

```php
SetValueInteger(12345, 0);
```

Jedes Setzen der Variable löst die Aktion aus, auch wenn sich der Wert nicht ändert. Wird das Skript also zweimal ausgeführt, wird auch die Aktion zweimal ausgelöst.
