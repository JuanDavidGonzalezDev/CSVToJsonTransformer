**Author:** Juan David Gonzalez Revelo

## Description:
A complete solution for the challenge was implemented: a CLI was added in `transform.php` that uses PSR-4 autoloading (`namespace App`) and three main classes under src/:
- `CsvUserReader` (streaming reader using a Generator to avoid loading the entire CSV into memory).
- `UserTransformer` (normalizes names, validates emails, converts signup_date to ISO-8601 UTC, rounds amount_spent and filters values < 10, maps country_code using countries.json, and computes loyalty_level).
- `JsonStreamWriter` (streams an output JSON array, creating the file if it doesn’t exist).

The solution uses PHP 8 strict typing, exception handling for read/write errors, and keeps memory usage low for large files.

Unit tests are in `UserTransformerTest.php` and cover key cases: valid row, invalid email, low amount, invalid date, and country fallback. The assertions check normalized values, rounding, and the loyalty_level logic; date validation uses a regular expression that ignores the time component to avoid failures due to timezone differences.

To run the tests: install PHPUnit as a development dependency and regenerate the autoloader, then run PHPUnit.


```bash
composer require --dev phpunit/phpunit
composer dump-autoload

vendor/bin/phpunit tests/UserTransformerTest.php

```
