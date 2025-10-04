# Windows Docker Compose

This document summarizes the changes made to add support for Docker Compose on Windows.

## Changes

- Added a `docker-compose.windows.yml` file to be used on Windows machines with Docker Desktop.
- This new docker-compose file maps the port 80 of the host to the port 80 of the container, so the application is accessible at http://localhost.
- Added a `windows/README.md` file with instructions on how to use the new docker-compose file.
- Added a `.htaccess` file to rewrite all requests to the `/web` directory. This means that you can access the application at http://localhost instead of http://localhost/web.
