## Add MySQL Volume to Docker Compose

This change introduces a named volume for the MySQL database service in `docker-compose.yml` to ensure data persistence. Previously, all MySQL data would be lost upon container removal or update. With this modification, the database data will be stored in a Docker volume, allowing it to persist across container lifecycle events.

**Changes Made:**

1.  A `volumes` section was added at the top level of `docker-compose.yml` to define a named volume called `db_data`.
2.  The `db` service configuration was updated to mount the `db_data` volume to `/var/lib/mysql`, which is the default data directory for MySQL.

**Purpose:**

To prevent data loss for the MySQL database when the Docker container is stopped, removed, or updated, thereby ensuring that the application's data remains intact.