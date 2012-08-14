• Jeśli masz już swój sklep OpenCart, przejdź do punktu 2. Jeśli nie masz sklepu - zainstaluj aplikację OpenCart na swoim hostingu, wybierając do instalacji wersję 1.5.3.1 (wtyczka integracyjna do ifirma.pl została przygotowana właśnie dla tej wersji oprogramowania sklepu. Oprogramowanie sklepu możesz pobrać np. ze strony OpenCart (http://www.opencart.com/).

• Pobierz przygotowaną przez ifirma.pl wtyczkę do integracji z OpenCart.

• Korzystając z menedżera FTP (np. FileZilla), w sklepie OpenCart, który wgrałeś na serwer, wejdź do katalogu o nazwie „sale”: admin/view/template/sale i wgraj do niego pliki z pobranej wcześniej wtyczki dla sklepu OpenCart 1.5.3.1:

folder „ifirma” (wgrać cały folder, a nie jego poszczególne pliki)
api_configuration.php
api_request.php
config.ini
download.php
order_list.tpl
setting.tpl

• Zatwierdź podmianę pliku order_list.tpl.

• Plik setting.tpl podmień dodatkowo w katalogu: admin/view/template/setting/setting.tpl.

• Wszystkie wgrane pliki (oraz katalog „ifirma”) powinny mieć uprawnienia “755”, prócz pliku config.ini, który powinien mieć uprawnienia „777”. Uprawnienia możesz edytować z poziomu menedżera FTP (zaznacz plik, kliknij prawym przyciskiem myszy i wybierz opcję „Prawa pliku”, wprowadź odpowiednią wartość uprawnienia i zatwierdź przyciskiem „OK”).

• Plik 001_ifirma_invoice_map.sql (w katalogu „ifirma”) zawiera zapytanie, które należy wysłać do stworzonej bazy danych sklepu OpenCart:

skopiowaną treść zapytania wklej do edytora zapytań MySQL z poziomu bazy danych, w phpMyAdmin: zakładka „SQL”, okno „wykonanie zapytań SQL do bazy danych”, zatwierdź przyciskiem „ wykonaj”. Utworzone zostaną tabele potrzebne do integracji.

• Teraz zaloguj się do panelu administracyjnego sklepu OpenCart, przejdź do zakładki System > Settings > Store, link na dole strony „Konfiguracja”. W celu skonfigurowania ustawień API, uzupełnij pola danymi pobranymi z Twojego konta w ifirma.pl (jeśli nie masz konta w ifirma.pl - zarejestruj się (https://www.ifirma.pl/cgi-bin/WebObjects/ifirma.woa/wa/register): administracja > ustawienia > ustawienia > klucze autoryzacji, sekcja „symetryczne klucze autoryzacji”):

klucz do API – faktura,
klucz do API – abonent,
login do API (login w ifirma.pl).

• Zapisz wprowadzone parametry. Pamiętaj, że jeśli w ifirma.pl przegenerujesz klucze autoryzacji, konieczne będzie wprowadzenie tych nowych kluczy również w konfiguracji API ifirma.pl sklepu OpenCart – zgodnie ze schematem przedstawionym w punkcie 8. Jeśli chcesz zintegrować z ifirma.pl inne sklepy, wszędzie użyj tych samych kluczy autoryzacji.

• Dla zamówień złożonych w Twoim zintegrowanym z ifirma.pl sklepie (Sales > Orders) można teraz wystawić fakturę (lub fakturę wysyłkowa), która zostanie automatycznie przesłana do ifirma.pl, gdzie będzie można ją zaksięgować.