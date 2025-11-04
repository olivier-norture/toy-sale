# Fix: Use X-Forwarded-For header to get real client IP

## Purpose

This change fixes an issue where the application would always see the Docker gateway IP address (e.g. `172.18.0.1`) for all clients instead of their real IP address. This was happening because the application was running inside a Docker container, and the Docker network was acting as a reverse proxy.

## Changes

### First fix

The file `classes/pages/page.php` was modified to use the `X-Forwarded-For` HTTP header to determine the client's IP address. This header is commonly added by reverse proxies to carry the original client IP address.

The code now checks for the existence of `$_SERVER['HTTP_X_FORWARDED_FOR']` and uses it if it's available. Otherwise, it falls back to `$_SERVER['REMOTE_ADDR']`.

This ensures that the application can correctly identify individual computers by their unique IP addresses, even when running behind a reverse proxy like Docker's networking layer.

### Second fix

It appeared that the `X-Forwarded-For` header could contain a comma-separated list of IP addresses. The previous fix was not correctly handling this case.

The files `classes/pages/page.php` and `web/index.php` were modified to parse the `X-Forwarded-For` header and take only the first IP address in the list.

This is done by using `explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]`.

### Third fix

Even with the previous fixes, requests made from the host machine (e.g. `curl localhost:8080`) were still showing the Docker bridge IP address.

To solve this for the development environment, the `docker-compose.yml` file was modified to use `network_mode: "host"` for the `app` service.

This makes the application share the host's network stack, and it will see the real client IP address. As a consequence, the application is now accessible on port 80 of the host, not 8080.
