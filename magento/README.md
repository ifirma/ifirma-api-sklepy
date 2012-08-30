

1. Jeśli masz już swój sklep Magento, przejdź do punktu 2. Jeśli nie masz sklepu - zainstaluj aplikację Magento na swoim hostingu, wybierając do instalacji wersję 1.7.0.2 (wtyczka integracyjna do ifirma.pl została przygotowana właśnie dla tej wersji oprogramowania sklepu). Oprogramowanie sklepu możesz pobrać np. ze strony Magento (http://www.magentocommerce.com/).

2. Pobierz przygotowaną przez ifirma.pl wtyczkę do integracji z Magento z github’a (https://github.com/ifirma/ifirma-api-sklepy/downloads) i zapisz ją na dysku komputera.

3. Korzystając z Menedżera FTP (np. FileZilla), w sklepie Magento, który wgrałeś na serwer, przejdź do katalogu o nazwie „local” (ścieżka: app/code/local). Jeśli w app/code nie ma folderu „local”, należy go utworzyć (kliknij prawym przyciskiem myszy > wybierz opcję „utwórz katalog” > nadaj mu nazwę „local” > zatwierdź przyciskiem „OK”). Do katalogu „local” wgraj pobrany z wtyczką folder „PowerMedia”(wgrać cały folder, a nie jego poszczególne pliki).

4. Następnie do katalogu: app/etc/modules należy wgrać plik PowerMedia_Ifirma.xml, a w katalogu: app/design/adminhtml/default/default/template/sales/order) wgrać pobrany z wtyczką plik totals.phtml – zatwierdzić podmianę i zmienić uprawnienia wgranego pliku na „755”). Żeby edytować uprawnienia pliku z poziomu menedżera FTP: zaznacz plik, kliknij prawym przyciskiem myszy opcję „Prawa pliku”, wprowadź odpowiednią wartość uprawnienia (np. „755”) i zatwierdź przyciskiem „OK”.

5. Dla pliku config.ini (ścieżka: app/code/local/PowerMedia/Ifirma/config.ini) zmień uprawnienia na „777”.

6. Plik 001_ifirma_invoice_map.sql (app/code/local/PowerMedia/Ifirma/ifirma) zawiera zapytanie, które należy wysłać do stworzonej bazy danych sklepu Magento:
skopiowaną treść zapytania wklej do edytora zapytań MySQL z poziomu bazy danych, w phpMyAdmin: zakładka „SQL”, okno „wykonanie zapytań SQL do bazy danych”
jeśli tabela Twojego sklepu Magento zawiera prefiks (np. „mage”), dodaj nazwę prefiksa we wklejonym zapytaniu (dotychczasowe „CREATE TABLE `ifirma`” przyjmię formę „CREATE TABLE `mage_ifirma`”);
 zatwierdź przesłanie zapytania przyciskiem „wykonaj”. Utworzone zostaną tabele potrzebne do integracji.

7. Teraz zaloguj się do panelu administratora sklepu i wyczyść cache Magento: System > Zarządzanie cache > „Flush Magento Cache”.

8. Pozostając w panelu administracyjnym sklepu Magento przejdź do zakładki „iFirma” > konfiguracja API. W celu skonfigurowania ustawień API, uzupełnij pola danymi pobranymi z Twojego konta w ifirma.pl (jeśli nie masz konta w ifirma.pl - zarejestruj się na https://www.ifirma.pl/cgi-bin/WebObjects/ifirma.woa/wa/register): administracja > ustawienia > ustawienia > klucze autoryzacji, sekcja „symetryczne klucze autoryzacji”):

klucz do API - faktura,
klucz do API - abonent
login do API (login w ifirma.pl),

Zapisz wprowadzone parametry. Pamiętaj, że jeśli w ifirma.pl przegenerujesz klucze autoryzacji, konieczne będzie wprowadzenie tych nowych kluczy również w konfiguracji API ifirma.pl sklepu Magento - zgodnie ze schematem przedstawionym w punkcie 8. Jeśli chcesz zintegrować z ifirma.pl inne sklepy, wszędzie użyj tych samych kluczy autoryzacji.

9. Dla zamówień złożonych w Twoim zintegrowanym z ifirma.pl sklepie (Sprzedaże > Zamówienia) można teraz wystawić fakturę (lub fakturę wysyłkową), która zostanie automatycznie przesłana do ifirma.pl, gdzie będzie można ją zaksięgować.
