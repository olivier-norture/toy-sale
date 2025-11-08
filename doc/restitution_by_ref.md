# Add participant search by reference on restitution page

This change allows users to search for a participant by their reference (e.g., A001) on the toy restitution page.

## Changes
- Added a new method `searchByRef` to the `Participant` class to find a participant by their reference.
- Added a search form on the `restitution_jouet.php` page.
- Modified the `RestitutionJouet` page class to handle the search and load the corresponding participant's data.

# Add participant search by ID on participant search page

This change allows users to search for a participant by their ID (reference) on the participant search page.

## Changes
- Added a new input field for the ID (reference) on the `ajout_participant.php` page.
- Modified the `AjouterParticipant` page class to handle the search by ID and display the result.

## Bug Fixes
- Fixed a fatal error on the restitution page when searching for a participant. The `Bill` object was created with no arguments, which is not allowed.
- Fixed a fatal error on the restitution page when an inactive bill was loaded. Added a defensive check to prevent calling a method on a null object.
- Fixed a bug on the restitution page when printing. An incorrect constructor call for the `Bill` object was causing an "inactive bill" error.
