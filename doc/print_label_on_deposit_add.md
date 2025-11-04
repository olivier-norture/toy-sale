# Print Label on Deposit Add

This change implements the functionality to automatically print a label for a newly added toy when the "Enregistrer" button is clicked on the deposit page (`depotjouet.php`).

## Changes Made:

1.  **`classes/pages/depotjouet.php`**:
    *   The `addObject()` method was modified to return the newly created `Objet` instance.
    *   The `pageProcess()` method was updated to redirect to `web/print_label.php` after a new object is added. This redirect includes the `objet_id` of the new toy and a `redirect_url` pointing back to the `depotjouet.php` page. An `exit()` call was added after the header redirect to ensure proper flow.

2.  **`web/print_label.php`**:
    *   The JavaScript `window.onload` function was modified. It now checks for a `redirect_url` GET parameter.
    *   If `redirect_url` is present, the page will redirect to that URL after triggering the print dialog.
    *   If `redirect_url` is not present, the original behavior of closing the window after printing is maintained.

## Purpose:

This feature streamlines the process of depositing toys by automatically generating and printing the corresponding label immediately after the toy's details are entered and saved. This reduces manual steps and ensures that each new toy gets its label promptly.
