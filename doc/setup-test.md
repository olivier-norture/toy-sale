## Test Setup

To address the lack of tests and facilitate the separation of backend and frontend logic, a PHPUnit testing environment has been set up.

### Steps Taken:

1.  **PHPUnit Installation and Configuration:**
    *   `phpunit/phpunit` was already listed in `composer.json` under `require-dev`.
    *   A `tests/` directory was created at the project root.
    *   A `phpunit.xml` configuration file was created to define the test suite and bootstrap the Composer autoloader.

2.  **Composer Autoloading:**
    *   The `composer.json` file was updated to include a `psr-4` autoloading rule for the `classes/` directory, mapping the `classes\` namespace to the `classes/` folder.
    *   `composer dump-autoload` was run to regenerate the autoloader files.

3.  **PSR-4 Compliance:**
    *   The file `classes/db/db.php` was renamed to `classes/db/DB.php` to comply with PSR-4 standards, where the filename must match the class name.

4.  **Initial Webpage Test (`IndexPageTest.php`):
    *   A test file `tests/IndexPageTest.php` was created to test the loading of `web/index.php`.
    *   **Session Handling:** The `session_start()` call in `classes/sessionmanager.php` was made conditional (`if (session_status() === PHP_SESSION_NONE && !headers_sent())`) to prevent "headers already sent" errors in the test environment.
    *   **Superglobal Mocking:** The `$_SERVER['REMOTE_ADDR']` variable was set to a dummy IP address (`127.0.0.1`) in the test's `setUp()` method to prevent "Undefined array key" errors.
    *   **Database Mocking:**
        *   The static `DB::$conn` property was set to a mock `PDO` object using PHP's Reflection API.
        *   The mock `PDO` object was configured to return a mock `PDOStatement` when `prepare()` is called.
        *   The mock `PDOStatement` was configured to return dummy data for `execute()`, `rowCount()`, `fetchAll()`, and `fetch()` to simulate database interactions for `ServerVar::find()` and `PC::search()`.
        *   This included adding a `counter` key to the mocked `PC` data to resolve an "Undefined array key 'counter'" error.
    *   **Assertion:** The test asserts that the output of `web/index.php` is not empty and contains the expected title `<title>Bourse aux Jouets Chambly</title>`.

### Current Status:

A basic functional test for `web/index.php` is now passing, demonstrating that the core dependencies can be mocked and the page can be loaded in a test environment. This provides a foundation for writing more comprehensive tests for individual webpages and their business logic.
