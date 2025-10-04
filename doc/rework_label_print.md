# Label Print Orientation Change

This document describes the changes made to the label printing functionality to switch from landscape to portrait orientation.

## Changes

The following changes were made to the `web/stylesheets/label.css` file:

- The `@page` size was changed from `62mm 30.48mm` to `30.48mm 62mm`.
- The `.label-container` width was changed from `62mm` to `30.48mm`.
- The `.label-container` height was changed from `30.48mm` to `62mm`.

## Purpose

The purpose of these changes is to ensure that the labels are printed in portrait orientation as requested by the user. This provides a better layout for the label content.
