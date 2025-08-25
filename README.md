**Author:** Juan David Gonzalez Revelo

### Requirements

1. **Input CSV format:**

   Columns: `id, first_name, last_name, email, signup_date, amount_spent, country_code`

   Example row:

   ```csv
   1234,John,Doe,john@example.com,2023-11-15,250.50,US
   ```

2. **Transformations:**

   - Normalize names (trim, capitalize).
   - Validate email format; discard invalid emails.
   - Convert `signup_date` to ISO 8601 UTC datetime string.
   - Round `amount_spent` to 2 decimals; filter out entries with `< 10`.
   - Map `country_code` to full country name from `countries.json`.
   - Add `loyalty_level` based on `amount_spent`:
     - `< 100`: Bronze
     - `100 - 500`: Silver
     - `> 500`: Gold

3. **Output:**

   - Write all valid transformed entries to `output.json`.
   - Output must be a valid JSON array.
   - Use streaming/generators to keep memory usage low.

---

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


### 🏃‍♂️ How to Run the CLI Script

1. Place your input CSV file in the project directory (e.g., `input.csv`).

2. Run the script using Docker:

   ```bash
   docker run --rm \
    -v "$PWD":/app \
    -w /app \
    -v "$PWD/xdebug-profiles":/tmp/xdebug \
    php-transform \
    php src/bin/transform.php --input=/app/input.csv --output=/app/output.json
   ```
