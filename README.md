# HomeKit für IP-Symcon 5.0

<a href="https://www.symcon.de"><img src="https://img.shields.io/badge/IP--Symcon-5.0-blue.svg?style=flat-square"/></a>
<a href="https://styleci.io/repos/100034267/"><img src="https://styleci.io/repos/100034267/shield" alt="StyleCI"></a>
<a href="https://travis-ci.org/paresy/HomeKit"><img src="https://img.shields.io/travis/paresy/HomeKit/master.svg?style=flat-square" alt="Build status"></a>
<br/>

_Diese Bibliothek kann nur mit dem noch nicht veröffentlichten IP-Symcon 5.0, welches PHP 7.2 enthält, genutzt werden. IP-Symcon 5.0 kommt wohl gegen Ende Q2/2018 - hoffentlich früh genug zur Apple HomePod Veröffentlichung in Deutschland. PHP 5.6 fehlen leider wichtige kryptographische Funktionen, sodass eine Unterstützung für IP-Symcon 4.x ausgeschlossen ist._ 

_Dieses Repository enthält keine von Apple zertifizierte Bridge. Außerdem wird es ausschließlich von der IP-Symcon Community (<a href="https://www.symcon.de/forum/members/1-paresy">paresy</a>, <a href="https://www.symcon.de/forum/members/10751-KaiS">KaiS</a>) gepflegt und nicht von der Symcon GmbH angeboten._

Folgende Module beinhaltet das Symcon HomeKit Repository:

- __HomeKit Bridge__ ([Dokumentation](HomeKitBridge))  
    Die Bridge kümmert sich um das Pairing und die Kommunikation zu den HomeKit Geräten
    
- __HomeKit Discovery__ ([Dokumentation](HomeKitDiscovery))  
    Das Discovery Modul kümmert sich um die Erkennung durch Apple Bonjour    