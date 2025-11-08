# Fix Incorrect Datetime Value

## Purpose

This change fixes a fatal PDOException that occurred when saving or updating objects with date fields. The error was "Incorrect datetime value" because the `STR_TO_DATE` MySQL function was being used with an incorrect format string.

This change also refactors the date handling to be backward compatible, supporting both `dd/mm/YYYY` and `YYYY-mm-dd` date formats.

## Changes

- A new utility function `format_to_sql` was added to `classes/utils/date.php`. This function can parse multiple date formats and returns a date string in the standard `YYYY-MM-DD HH:MI:SS` format.
- The `save()` method in `classes/db/object/objet.php` was updated to use this new function.
- The `STR_TO_DATE` calls were removed from the SQL queries in `classes/db/object/objet.php`, as the dates are now pre-formatted in PHP.
