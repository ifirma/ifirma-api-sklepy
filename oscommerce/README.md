IFIRMA API - osComerce

- Aby zainstalować osCommerce (opis wykonany do wersji `osCommerce 2.3.1`), należy mieć dostęp do zdalnego serwera internetowego lub do lokalnego serwera na komputerze (MAMP – dla Mac OS-a, LAMP – dla Linuksa, WAMP – dla Windows) z zainstalowanym system bazodanowym, jak np. MySQL.

- Za pośrednictwem phpMyAdmin (panelu administracyjnego systemu bazodanowego) tworzymy bazę danych, którą wpisujemy do instalatora osCommerce. Panel administracyjny phphMyAdmin pozycja MYSQL localhost > Utwórz nową bazę danych> podajemy własną nazwę (np. „baza oscommerce”).

- Następnie pobieramy darmowe oprogramowanie sklepu internetowego osCommerce ze strony www.oscommerce.com/solutions/downloads (`osCommerce online Merchant v2.3.1`). Kopiujemy rozpakowaną bazę sklepu na serwer.

- Dwóm plikom nadajemy odpowiednie uprawnienia:

/nasz_katalog/includes/configure.php > chmod 777 includes/configure.php
/nasz_katalog/admin/includes/configure.php > chmod 777 admin/includes/configure.php
Wchodzimy na naszą domenę np. http://www.nasza_domena.pl i rozpoczynamy instalację zgodnie ze wskazówkami wyświetlanymi na ekranie podając w Database configuration:

nazwę bazy danych serwera (np. localhost),
login jaki podajemy łącząc się z bazą danych phpMyAdmin (np. root)
hasło jaki podajemy łącząc się z bazą danych phpMyAdmin (np. root),
nazwę bazy danych stworzoną z poziomu administratora phpMyAdmin (np. baza oscommerce).
Dalej postępujemy zgodnie z komunikatami pojawiającymi się w miarę procesu instalacji, po której ze względów bezpieczeństwa usuwamy z serwera katalog install oraz zmieniamy nazwę katalogu admin na inną (np. admin123).

- Ponownie zmieniamy uprawnienia do plików:

/nasz_katalog/includes/configure.php > chmod 644 includes/configure.php
/nasz_katalog/admin/includes/configure.php > chmod 644 admin/includes/configure.php
Następnie stąd pobieramy pliki niezbędne do integracji sklepu internetowego z ifirma.pl. Do naszego folderu (np. admin123) należy skopiować:

folder ifirma/* pliki: - api_configuration.php - api_request.php - config.ini - download.php - orders.php

- Wszystkie pliki powinny mieć odpowiednie uprawnienia (755, a w przypadku config.ini 777). (przykładowa komenda chmod 755 api_request.php)

- Plik `001_ifirma_invoice_map.sql` zawiera zapytanie, które należy wysłać do stworzonej bazy danych osCommerce. Treść zapytania można wkleić np. do edytora zapytań MySQL z poziomu narzędzi administracyjnych bazy danych takich jak phpMyAdmin. Stworzone zostaną niezbędne tabele potrzebne do integracji. Dodatkowo wykonanie tego zapytania utworzy dodatkową tabelę w bazie danych.

- Przed skorzystaniem z API należy dokonać konfiguracji za pomocą wygenerowanych uprzednio w serwisie ifirma.pl kluczy symetrycznych dla abonenta i faktury oraz za pomocą swojego loginu do serwisu ifirma.pl. Konfiguracji należy przeprowadzić z poziomu panelu administratora sklepu (zakładka Customers->Orders->konfiguracja API).

- Następnie można już wystawić fakturę, która pojawi się w przychodach na naszym koncie w ifirma.pl
