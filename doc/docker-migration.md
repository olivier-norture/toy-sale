# Docker Migration

## Context

Actually the application run in a virtual machine through VirtualBox.

## Needed refactoring
- Review constants.php because some values are now from environment variable we need to review configuration management to be instanciated since it's no more a static value (like DB_HOST).
- Refactor createDb.sql and insert.sql script used by db.php to initialization
    - allow to change the db name in the .sql script

## Server update
Actually server's update are done via a git command that updates source using git.

I will replace it with a docker version on the frontend and the backend.

So for frontend I need to ask for version reload?

And what about the backend? It means I have to (somehow) update the container and update (if needed) the data. So I could allow upgrade but not downgrade.

### Using a script
```bat
@echo off
echo Stopping and updating MyApp...
docker compose pull
docker compose up -d
echo Done!
pause
```
### Database Migration

An automated database migration system has been implemented to handle database schema and data updates.

-   **How it works:**
    -   A new table named `migrations` has been created in the database to keep track of which update scripts have been executed.
    -   On application startup, the system automatically scans the `misc/update/` directory for any new `.sql` script files.
    -   It then executes any new scripts that have not yet been applied and records them in the `migrations` table.

-   **Removed Components:**
    -   The old manual update system, which involved `web/update.php` and `git-update.sh`, has been removed.
    -   The "Mise à jour du serveur" link has been removed from the application's header.

This new system ensures that database updates are applied automatically and consistently, which is essential for a Docker-based deployment.
