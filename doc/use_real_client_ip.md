
# Fix: Use X-Forwarded-For header to get real client IP

## Purpose

This change fixes an issue where the application would always see the Docker gateway IP address (e.g. `172.18.0.1`) for all clients instead of their real IP address. This was happening because the application was running inside a Docker container, and the Docker network was acting as a reverse proxy.

## Changes

The file `classes/pages/page.php` was modified to use the `X-Forwarded-For` HTTP header to determine the client's IP address. This header is commonly added by reverse proxies to carry the original client IP address.

The code now checks for the existence of `$_SERVER['HTTP_X_FORWARDED_FOR']` and uses it if it's available. Otherwise, it falls back to `$_SERVER['REMOTE_ADDR']`.

This ensures that the application can correctly identify individual computers by their unique IP addresses, even when running behind a reverse proxy like Docker's networking layer.
