# Fix Print Popup Closing Prematurely in Chrome and Print After Adding or Updating a Toy

This document describes the changes made to the label printing functionality to fix an issue where the print popup would close prematurely in Google Chrome and to print the label of the newly added or updated toy.

## Problem

The print popup, which is opened to print labels, was closing immediately after the print dialog was displayed in Google Chrome. This prevented users from being able to print labels.

Also, the user wanted to print the label of the newly added or updated toy after adding or updating it.

## Solution

The first issue was resolved by adding a `setTimeout` to the `window.close()` call in `web/print_label.php`. This gives the browser enough time to open the print dialog before the window is closed.

I also improved the `imprimer_page` function in `web/js/script.js` to use `onafterprint` and a fallback with `setTimeout`. This makes the print function more robust and will work better across different browsers.

To print the label of the newly added or updated toy, I did the following changes:

- In `classes/pages/depotjouet.php`:
    - In `addObject()`, after saving the new object, I store its ID in the session.
    - In the `update` case in `pageProcess()`, I store the object ID in a session variable.
    - I added cases in `pageProcess()` to clear the session variables.
- In `web/depot_jouet.php`:
    - In the `window.onload` function, I added a check for the new object's ID and the updated object's ID in the session.
    - If one of them is present, it calls `printLabel()` with the ID and then reloads the page to clear the session variable.

The following files were modified:

- `web/print_label.php`
- `web/js/script.js`
- `classes/pages/depotjouet.php`
- `web/depot_jouet.php`
