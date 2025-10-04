# Docker Publish Workflow

This workflow builds and publishes the Docker image to the GitHub Container Registry.

## Trigger

The workflow is triggered on every push to the `main` branch.

## Functionality

The workflow consists of a single job, `build-and-publish`, which performs the following steps:

1.  **Checkout repository**: Checks out the source code of the repository.
2.  **Log in to the GitHub Container Registry**: Logs in to the GitHub Container Registry using a temporary token.
3.  **Build and push Docker image**: Builds the Docker image using the `Dockerfile` in the root of the repository and pushes it to the GitHub Container Registry. The image is tagged with `dev`.

## Image Name

The image is published under the following name:

`ghcr.io/<owner>/<repository>:dev`

Where `<owner>` is the owner of the repository and `<repository>` is the name of the repository.
