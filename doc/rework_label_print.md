# Label Print Orientation Change

This document describes the changes made to the label printing functionality to switch from landscape to portrait orientation.

## Changes

The following changes were made to the `web/stylesheets/label.css` file:

- The `@page` size was changed from `62mm 30.48mm` to `30.48mm 62mm`.
- The `.label-container` width was changed from `62mm` to `30.48mm`.
- The `.label-container` height was changed from `30.48mm` to `62mm`.

## Purpose

The purpose of these changes is to ensure that the labels are printed in portrait orientation as requested by the user. This provides a better layout for the label content.

## Price Position Change

The position of the price on the label has been moved to the bottom right.

### Changes

- **`web/print_label.php`**:
  - The HTML structure of the label was modified to separate the reference, description, and price into their own `div` elements.
- **`web/stylesheets/label.css`**:
  - The CSS was updated to position the price at the bottom of the label with right alignment.
  - The `.label-ref-price` class was replaced by `.label-ref` and a new `.label-price` class was added.

### Purpose

This change was made to improve the layout of the label and place the price in a more logical position after the description.

## Font Size Change

The font sizes on the label have been doubled.

### Changes

- **`web/stylesheets/label.css`**:
  - The font size for `.label-ref` was changed from `12pt` to `24pt`.
  - The font size for `.label-description` was changed from `8pt` to `16pt`.
  - The font size for `.label-price` was changed from `12pt` to `24pt`.

### Purpose

This change was made to improve the readability of the label.

## Line Feed Addition

A line feed has been added between the object reference and the description.

### Changes

- **`web/stylesheets/label.css`**:
  - A `margin-top` of `1mm` was added to the `.label-description` class.

### Purpose

This change was made to improve the visual separation between the object reference and the description.