# Fix Print Popup Closing Prematurely in Chrome and Print After Adding a Toy without API

This document describes the changes made to the label printing functionality to fix an issue where the print popup would close prematurely in Google Chrome and to print the label of the newly added toy without using an API.

## Problem

The print popup, which is opened to print labels, was closing immediately after the print dialog was displayed in Google Chrome. This prevented users from being able to print labels.

Also, the user wanted to print the label of the newly added toy after adding it.

## Solution

The first issue was resolved by adding a `setTimeout` to the `window.close()` call in `web/print_label.php`. This gives the browser enough time to open the print dialog before the window is closed.

To print the label of the newly added toy, I did the following changes:

- In `web/print_label.php`:
    - I modified the page to accept the label data from GET parameters.
- In `web/depot_jouet.php`:
    - I modified the "Enregistrer" button to call a JavaScript function that opens the print label page with the new toy's data in the URL and then submits the form.

The following files were modified:

- `web/print_label.php`
- `web/depot_jouet.php`