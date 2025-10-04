# Toy Lifecycle and Deposit Flow

This document outlines the lifecycle of a toy within the system, from its initial registration to its final state (sold, returned, etc.), as well as the process for generating and printing labels.

## Toy States

A toy progresses through several states during its lifecycle.

*   **DRAFT**: A toy is in this state when it is first registered but the deposit is not yet finalized. It is not visible for sale.
*   **ON SALE**: Once the deposit is completed, the toy becomes available for sale.
*   **SOLD**: The toy has been sold and paid for.
*   **CANCELED**: The deposit of the toy has been canceled by the seller before it was sold.
*   **RESTITUTED**: The toy has been returned to the seller. This can happen from the `DRAFT` or `ON SALE` state (e.g., if an issue is found).
*   **PAID**: The seller has received the money for a sold toy.

### State Transitions

Here is a summary of the possible state transitions:

- `DRAFT` -> `ON SALE` (When the deposit is finalized)
- `DRAFT` -> `CANCELED` (If the seller cancels the deposit)
- `DRAFT` -> `RESTITUTED` (If the toy is returned to the seller before being put on sale)
- `ON SALE` -> `SOLD` (When the toy is sold)
- `ON SALE` -> `DRAFT` (Can be reverted if an issue is discovered, e.g., a missing piece, to prevent it from being sold)
- `ON SALE` -> `RESTITUTED` (If the toy is returned to the seller)
- `SOLD` -> `PAID` (When the money is given to the seller)

### Special Cases

*   **Stolen Toys**: If a toy is stolen, it should be marked as `SOLD` and attributed to a special internal account, as the organization is responsible for it.

## Deposit Process

The deposit process is centered around the seller.

*   **Seller ID**: Each seller has a unique, persistent ID. This ensures that all toy registrations are consistently linked to the same seller, even across different sessions or computers.
*   **Single Deposit**: A seller has only one active deposit at a time. When a seller registers new toys, they are added to their current deposit in the `DRAFT` state.
*   **Finalization**: When the seller finalizes the deposit, all associated toys in the `DRAFT` state are moved to `ON SALE`.

## Label Generation and Printing

For each toy, a printable label can be generated from the `depot_jouet.php` page.

### Label Specifications

*   **Size**: The label is designed for a 62mm width format.
*   **Layout**: The label uses a single-column flexbox layout.
    *   **Top Row**: Contains the toy's reference number on the left and the price on the right.
    *   **Bottom Section**: Contains the toy's description, which fills the remaining vertical space on the label.

### Styling and HTML Structure

The layout is controlled by `web/stylesheets/label.css` and the structure is defined in `web/print_label.php`.

*   **Top Row (`.label-ref-price`)**:
    *   Uses `display: flex` and `justify-content: space-between` to position the reference and price.
    *   Font: Arial, 62pt.
*   **Bottom Section (`.label-description`)**:
    *   Uses `flex-grow: 1` to fill available space.
    *   Font: Arial, 40pt.
    *   Text is justified (`text-align: justify`) and overflow is hidden.

### Printing Mechanism

*   Clicking the print button for a toy opens a new browser popup window (`web/print_label.php`) containing the formatted label.
*   The popup automatically triggers the browser's print dialog.
*   The popup window closes automatically after the user interacts with the print dialog (whether printing or canceling), with fallback mechanisms for cross-browser compatibility.

## Open Questions & TODOs

### Questions
*   Should the system save toys one by one during a large deposit, or as a single transaction? For sales, it seems less critical as it can be redone.

### TODO
- [ ] Consider renaming the `objet` entity to `toy` in the codebase for clarity.
- [ ] Implement validation logic to prevent accidental deletion of a toy.
- [ ] Add the `state` attribute to the toy entity in the database and code.