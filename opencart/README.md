Wtyczka kombatybilna z wersją OpenCart 1.5+ oraz 2.0+  

Wtyczka nie obsługuje rabatów kwotowych i procentowych od całości zamówienia.

Wtyczka do działania wymaga obecności vQmod w najnowszej wersji: https://github.com/vqmod/vqmod/releases

Aby zainstalować wtyczkę należy skopiować zawartość projektu (katalogi admin, builder, connector, manager, vqmod) do katalogu głównego sklepu (katalog, w którym znajduje się katalog 'admin') 

Po skopiowaniu katalogów projektu do katalogu głównego sklepu należy sprawdzić:
 - pliki index.php w katalogu głównym i admin/index.php mają uprawnienia 755, jeśli nie mają to trzeba je zmienić (jeśli nie działa z tymi uprawnieniami to można spróbować po zmianie ich na 777)
 - katalog vqmod (i wszystkie pliki i katalogi w vqmod) mają mieć uprawnienia 755 [takie są domyślnie ustawione, sprawdzać i ewentualnie zmieniać tylko w przypadku, gdyby coś nie działało]
 - plik .htaccess w katalogu głównym ma mieć uprawnienia 644 [takie są domyślnie ustawione, sprawdzać i ewentualnie zmieniać tylko w przypadku, gdyby coś nie działało]
 
W przypadku komunikatu błędu o nieprawidłowej stawce podatku VAT przy próbie wystaweinia dokumentu należy sprawdzić ustawienia podatku VAT w sklepie, w szczególności ustaweinia:
- stref geograficznych,
- progów podatkowych,
- stawek podatkowych,
- użycie adresu podatkowego sklepu.
 
Wtyczka do działania z najnowszymi wersjami OpenCart została dostosowana przez Redigart Design www.redigart.com 