Ostrzeżenie – nie instaluj nowej wersji wtyczki, jeżeli Twój sklep jest już zintegrowany z ifirma.pl (w pzypadku gdy poprzednia wtyczka nie została odinstalowana).

Instrukcja:
-----------
 - Wtyczkę instaluje się po zainstalowaniu sklepu
 - Pobierz wtyczkę www.ifirma.pl/wp-content/uploads/2013/08/ifirma-magento-0.1.0.zip
 - Zaloguj się do panelu administracyjnego Twojego sklepu
 - Przejdź do zakładki „System” → „Magento Connect” → „Zarządzanie Magento Connect”
 - Po zalogowaniu do Magento Connect Managera (dane takie jak do panelu administracyjnego sklepu) jesteś w zakładce „Extensions”. Znajdź na stronie sekcję „Direct package file upload”, wybierz plik (przycisk „Przeglądaj”), który pobrałeś w kroku drugim i kliknij „Upload”. W oknie na dole ekranu powinien pojawić się komunikat:

„Package installed:
community <nazwa_pliku_który_pobrałeś>
Cleaning cache
Cache cleaned successfully

Wróć do panelu administracyjnego sklepu Magento (link „Return to Admin” w prawym górnym rogu ekranu Magento Connect Managera). W panelu administracyjnym wejdź do zakładki „System” → „Konfiguracja” i w menu po lewej stronie ekranu znajdź podmenu (powinno być pierwsze od góry) „IFIRMA” → „Konfiguracja”. W celu skonfigurowania ustawień API uzupełnij pola danymi pobranymi z Twojego konta w ifirma.pl: konfiguracja → integracje i usługi → klucze autoryzacji, sekcja „Symetryczne klucze autoryzacji”:
    klucz do API – rachunek
    klucz do API – faktura
    klucz do API – abonent
    login do API (login w ifirma.pl).

Zapisz wprowadzone parametry. Pamiętaj, że jeśli w ifirma.pl przegenerujesz klucze autoryzacji, konieczne będzie wprowadzenie tych nowych kluczy również w konfiguracji API ifirma.pl sklepu Magento. Jeśli chcesz zintegrować z ifirma.pl inne sklepy, wszędzie użyj tych samych kluczy autoryzacji.

Dla zamówień złożonych w Twoim zintegrowanym z ifirma.pl sklepie (Sprzedaże → Zamówienia) można teraz wystawić fakturę krajową, fakturę wysyłkową lub proforma, która zostanie automatycznie przesłana do ifirma.pl, gdzie będzie można ją zaksięgować.

Uwagi:
------
 - polskie stawki VAT(23%, 8%, 5%, 0%) należy samodzielnie zdefiniować w konfiguracji sklepu
 - nie ma możliwości ustawienia stawki VAT 'zw' (sklep nie daje możliwości zdefiniowana stawki z wartością inną niż liczba)
 - sprzedaż w walucie obsługiwana jest na zasadzie 1:1, czyli w sklepie 1USD = 1PLN na fakturze
