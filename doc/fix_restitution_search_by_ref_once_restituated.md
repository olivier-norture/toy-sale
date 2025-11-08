# Fix participant search by reference after restitution

## Summary of Changes

The participant search by reference was failing after the restitution process because it required an active "depot" bill. This was problematic because the restitution process deactivates the depot bill.

This change modifies the `Participant::searchByRef` method in `classes/db/object/participant.php` to remove the `active = true` condition from the SQL query. This allows participants to be found by their reference number even after their initial depot bill has been deactivated during restitution, ensuring consistent searchability.

## Purpose of the change

The purpose of this change is to fix a bug that prevented users from searching for a participant by their reference number after the restitution process was completed. This improves the user experience and makes the system more robust.
