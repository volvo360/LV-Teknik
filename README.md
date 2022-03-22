LV Teknik

Ett projekt som har gjort på uppdrag av vän som har en bifirma inom offentliga fontäner och belysning.

Detta är andra versionen av hemsidan, mycket har förändrats mycket sedan den första utgåvan och har mer eller mindre designats om från början. Tanken var att tag fram en hemsida som de själva skulle kunna editera innehållet och annan struktur på hemsidan. Dock är de låsta till själva templaten. 

Sidan är uppbyggd som så att alla texter kan förekomma på flera olika språk om de vill vända sig internationellt och väljer att satsa på företaget men det verkar inte troligt. Även här har jag haft högt säkerhets tänk från början, är dock medveten om att inga system är helt säkra för illasinnade besökare utan man kan bara försvåra för dem genom unika strängar istället för id själva sidan som de kan tag del, medan man jobbar med vanliga id internt i koden. Krypterar det mesta av datan i databasen, samt lagrar bara lösenords hashar i databasen. All information som kommer utifrån anses vara ”smutsig” och ”tvättas” av koden för att undvika SQL-injektions.

Jag tar inget ansvar för innehållet på sidan eller att den uppdateras med jämna mellanrum, försökte att få in lite säljande texter till dem osv. Men intresset för att hålla hemsidan aktuell verkar vara väldigt dåligt enligt mig. De är inte helt medvetna om hur viktigt det är med en säljande hemsida osv.

De kan ladda upp texter till referensprojekt och arbeten, samt bilder. Det är förberett för andra typer av grupper av projekt eller indelningar *Vet ej hur man ska utrycka det*.

Besök gärna https://www.lvteknik.se/

Det står på sidan att den är utvecklad av Binarysolutions vilket är ett icke existerande företag. Planerna föl i sanden så att säga, tanken var att marknadsföra en tjänst för att kunna importera prislistor som man har en butik till ens open source webbshop. Sidan fungerade rätt bra, men vi hade lite prestandaproblem när bilderna skulle skalas om ute hos kunden. Planen var att ha så lite data hos oss och lägga så mycket som möjligt av ”arbetet” på kundens servrar. Man kunde justera olika produktegenskaper så som storlek och färg på en artikel osv. Just personen som jag kom i kontakt med sålde golfartiklar och ville ha till ett enklare sätt att uppdatera sin hemsida via prisfilerna som han arbetade internt med. Antar att det har göra med att PHP 5 serien hade dåligt stöd GD-image om jag nu minns rätt på vad programmet heter som ingår i Linux, vilket används av nästa alla webhotell med låga till rimliga priser.
