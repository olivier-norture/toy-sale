# Fix Print Popup Closing Prematurely in Chrome

This document describes the changes made to the label printing functionality to fix an issue where the print popup would close prematurely in Google Chrome.

## Problem

The print popup, which is opened to print labels, was closing immediately after the print dialog was displayed in Google Chrome. This prevented users from being able to print labels.

## Solution

The issue was resolved by adding a `setTimeout` to the `window.close()` call in `web/print_label.php`. This gives the browser enough time to open the print dialog before the window is closed.

I also improved the `imprimer_page` function in `web/js/script.js` to use `onafterprint` and a fallback with `setTimeout`. This makes the print function more robust and will work better across different browsers.

The following files were modified:

- `web/print_label.php`
- `web/js/script.js`