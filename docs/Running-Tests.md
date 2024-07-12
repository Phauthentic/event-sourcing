# Running Tests

## Running Tests via Docker

Running `make` in the root of the repository will show you a list of available make commands.

```
Available commands:
  - run-tests:       Run tests
  - run-infection:   Runs Infection mutation testing
  - coverage-text:   Runs coverage text
  - coverage-html:   Runs coverage html
  - shell:           Run shell
```

You can run tests in the Docker container by running `make run-tests`. It is strongly recommended to run tests in the Docker container to ensure that the environment is consistent across all developers.
