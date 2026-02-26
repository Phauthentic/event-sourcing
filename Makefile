.DEFAULT_GOAL := help

DOCKER_RUN = docker compose run --rm php

help:
	@echo "Available commands:"
	@echo "  - run-tests:       Run PHPUnit tests"
	@echo "  - run-infection:   Run Infection mutation testing"
	@echo "  - coverage-text:   Run tests with coverage (text output)"
	@echo "  - coverage-html:   Run tests with coverage (HTML report)"
	@echo "  - phpstan:         Run PHPStan static analysis"
	@echo "  - cscheck:         Run PHP_CodeSniffer (check only)"
	@echo "  - csfix:           Run PHP_CodeSniffer (fix)"
	@echo "  - phpmd:           Run PHP Mess Detector"
	@echo "  - benchmark:       Run PHPBench benchmarks"
	@echo "  - all:             Run CS-Fixer, CS-Checker, PHPStan and Tests"
	@echo "  - shell:           Open shell in container"

run-tests:
	@echo "Running tests"
	$(DOCKER_RUN) composer test

run-infection:
	@echo "Running infection mutation testing"
	$(DOCKER_RUN) composer infection

coverage-text:
	@echo "Running coverage (text)"
	$(DOCKER_RUN) composer test-coverage

coverage-html:
	@echo "Running coverage (HTML)"
	$(DOCKER_RUN) composer test-coverage-html

phpstan:
	@echo "Running PHPStan"
	$(DOCKER_RUN) composer analyze

cscheck:
	@echo "Running PHP_CodeSniffer (check)"
	$(DOCKER_RUN) composer cscheck

csfix:
	@echo "Running PHP_CodeSniffer (fix)"
	$(DOCKER_RUN) composer csfix

phpmd:
	@echo "Running PHP Mess Detector"
	$(DOCKER_RUN) composer phpmd

benchmark:
	@echo "Running phpbench"
	$(DOCKER_RUN) composer benchmark

all:
	@echo "Running CS-Fixer, CS-Checker, PHPStan and Tests"
	$(DOCKER_RUN) composer all

shell:
	@echo "Opening shell"
	docker compose run --rm --service-ports --entrypoint /bin/bash php
