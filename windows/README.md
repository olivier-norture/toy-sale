# Windows Docker Desktop Setup

This guide explains how to set up and run the application on a Windows machine using Docker Desktop.

## Prerequisites

- Make sure you have Docker Desktop installed and running on your Windows machine.

## How to run the application

1. Open a PowerShell or Command Prompt terminal.
2. Navigate to the root directory of the project.
3. Run the following command:

```
docker-compose -f docker-compose.windows.yml up -d
```

4. The application should now be accessible at http://localhost.

## Important Notes

- This setup uses the `docker-compose.windows.yml` file, which is specifically configured for Windows environments.
- The application will be available on port 80, so you can access it directly via `http://localhost`.
- The `-d` flag runs the containers in detached mode, so they will run in the background.

## How to stop the application

To stop the application, run the following command in the same directory:

```
docker-compose -f docker-compose.windows.yml down
```
