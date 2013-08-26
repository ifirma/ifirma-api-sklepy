Wtyczka kombatybilna z wersją OpenCart 1.5  (nie obsługuje rabatów kwotowych i procentowych od całości zamówienia).

Aby zainstalować wtyczkę należy skopiować zawartość projektu (katalogi admin, builder, connector, manager, vqmod) do katalogu głównego sklepu (katalog, w którym znajduje się katalog 'admin') oraz zainstalować vQmod zgodnie z instrukcją zamieszczoną na stronie http://code.google.com/p/vqmod/wiki/Install_OpenCart (źródła vQmod dodane są już do projektu). W przypadku świeżej instalacji sklepu OpenCart należy w panelu administratora zdefiniować stawki VAT (System->Localisation->Taxes->TaxRates) i przypisać je do odpowiednich stref (System->Localisation->GeoZones), a następnie w sekcji TaxClasses (System->Localisation->Taxes->TaxClasses) zdefiniować klasy podatków, które są przypisywane do produktów lub metod wysyłki.

Po skopiowaniu katalogów projektu do katalogu głównego sklepu należy sprawdzić:
 - pliki index.php w katalogu głównym i admin/index.php mają uprawnienia 755, jeśli nie mają to trzeba je zmienić (jeśli nie działa z tymi uprawnieniami to można spróbować po zmianie ich na 777)
 - katalog vqmod (i wszystkie pliki i katalogi w vqmod) mają mieć uprawnienia 755 [takie są domyślnie ustawione, sprawdzać i ewentualnie zmieniać tylko w przypadku, gdyby coś nie działało]
 - plik .htaccess w katalogu głównym ma mieć uprawnienia 644 [takie są domyślnie ustawione, sprawdzać i ewentualnie zmieniać tylko w przypadku, gdyby coś nie działało]
 - w nowo zainstalowanym sklepie oprócz powyższych zmian trzeba jeszcze przypisać odpowiednią klasę podatkową dla wybranych przez nas metod dostarczania towaru (Extensions ->  Shipping -> Edit (wybrane metody) -> Tax Class (wybrać z listy rozwijanej)

