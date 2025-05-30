# Contributing

## Code style

StyleCI will flag code style violations in your pull requests.

## Running tests

Run `docker-compose up -d` to start the containers. Then run `./test` to run the tests.

When you're done testing, run `docker-compose down` to shut down the containers.

### Docker on M1

You can add:
```yaml
services:
  mysql:
    platform: linux/amd64
  mysql2:
    platform: linux/amd64
```

to `docker-compose.override.yml` to make `docker-compose up -d` work on M1.
