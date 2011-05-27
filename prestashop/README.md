IFIRMA API - PrestaShop
=======================


- Aby zainstalować PrestaShop (opis wykonany do wersji `PrestaShopPL_1.4`), należy mieć dostęp do zdalnego serwera internetowego lub do lokalnego serwera na komputerze (MAMP – dla Mac OS-a, LAMP – dla Linuksa, WAMP – dla Windows) z zainstalowanym system bazodanowym, jak np. MySQL.

- Za pośrednictwem phpMyAdmin (panelu administracyjnego systemu bazodanowego) tworzymy bazę danych, którą wpisujemy do instalatora PrestaShopu. Panel administracyjny phphMyAdmin pozycja MYSQL localhost > Utwórz nową bazę danych> podajemy własną nazwę (np. baza prestashop).

- Następnie pobieramy darmowe oprogramowanie sklepu internetowego PrestaShop ze strony internetowej www.prestashop.pl/pliki.html.

- Kopiujemy rozpakowaną bazę sklepu na serwer, wchodzimy na naszą domenę np. http://www.nasza_domena.pl i rozpoczynamy instalację zgodnie ze wskazówkami zawartymi w instrukcji http://www.prestashop.pl/tutoriale.html, podając w Database configuration:

nazwę bazy danych serwera (np. localhost),
nazwę bazy danych stworzoną z poziomu administratora phpMyAdmin (np. baza prestashop),
login, który podajemy łącząc się z bazą danych phpMyAdmin (np. root),
hasło, które podajemy łącząc się z bazą danych phpMyAdmin (np. root).
Dalej postępujemy zgodnie z komunikatami pojawiającymi się w miarę procesu instalacji, po której ze względów bezpieczeństwa usuwamy z serwera katalog install oraz zmieniamy nazwę katalogu admin na inną (np. admin123)

Następnie stąd pobieramy pliki niezbędne do integracji sklepu internetowego z ifirma.pl.

Do poprzednio utworzonego folderu (np. admin123) należy skopiować:

folder ifirma/*
api_configuration.php
api_request.php
config.ini
download.php
do podmiany/modyfikacji jest plik PrestaShopu admin123/tabs/AdminOrders.php

- wszystkie pliki powinny mieć odpowiednie uprawnienia (755, a w przypadku config.ini 777). (przykładowa komenda chmod 755 api_request.php)

- Plik `001_ifirma_invoice_map.sql` zawiera zapytanie, które należy wysłać do stworzonej bazy danych PrestaShopu. Treść zapytania można wkleić np. do edytora zapytań MySQL z poziomu narzędzi administracyjnych bazy danych, takich jak phpMyAdmin. Utworzone zostaną niezbędne tabele potrzebne do integracji. Dodatkowo wysłanie tego zapytania utworzy dodatkową tabelę w bazie danych.

- Przed skorzystaniem z API należy go skonfigurować za pomocą wygenerowanych uprzednio w serwisie ifirma.pl kluczy symetrycznych dla abonenta i faktury oraz za pomocą swojego loginu do usługi ifirma.pl. Konfigurację należy przeprowadzić z poziomu panelu administratora sklepu (zakładka Zamówienia -> Szczegóły/podgląd zamówienia -> Konfiguracja API).

- Następnie można już wystawić fakturę, która pojawi się w przychodach na naszym koncie w serwisie ifirma.pl.
