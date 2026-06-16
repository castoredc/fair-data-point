# FAIR Data Point — developer task runner.
# Recipes run the PHP tooling inside the `symfony` container (WORKDIR /home/castor/fdp),
# so PHP version and extensions always match production. Requires the docker-compose
# stack to be running (`just up`).

# Run `docker compose exec` against the symfony container.
exec := "docker compose exec symfony"

# List available recipes.
default:
    @just --list

# --- Tests -----------------------------------------------------------------

# Run the full test suite (unit + integration).
test: test-unit test-integration

# Run the unit suite. Extra args pass through, e.g. `just test-unit --filter VoterTest`.
test-unit *args:
    {{exec}} vendor/bin/phpunit --testsuite unit {{args}}

# Run the integration suite (boots the kernel against the fdp_test database).
test-integration *args:
    {{exec}} vendor/bin/phpunit --testsuite integration {{args}}

# Run both suites with coverage; HTML report lands in var/coverage.
coverage:
    {{exec}} php /usr/bin/composer test:coverage

# Refresh the committed RDF snapshot fixtures, then review the diff before committing.
update-rdf-snapshots:
    {{exec}} env UPDATE_RDF_SNAPSHOTS=1 vendor/bin/phpunit --testsuite unit

# --- Static analysis & coding standards ------------------------------------

# Run PHPStan (level 7).
stan:
    {{exec}} php -d memory_limit=4G vendor/bin/phpstan analyse --no-progress

# Check coding standards (Castor ruleset).
cs *args:
    {{exec}} vendor/bin/phpcs {{args}}

# Auto-fix coding standard violations (exits non-zero when it fixes things).
fix *args:
    -{{exec}} vendor/bin/phpcbf {{args}}

# The full local quality gate — mirrors what CI will run per PR.
check: test-unit test-integration stan cs

# --- Helpers ---------------------------------------------------------------

# Run a Symfony console command, e.g. `just console debug:router`.
console *args:
    {{exec}} bin/console {{args}}

# Run a Composer command in the container, e.g. `just composer require foo/bar`.
composer *args:
    {{exec}} php /usr/bin/composer {{args}}

# Open an interactive shell in the symfony container.
shell:
    {{exec}} sh

# --- Stack lifecycle -------------------------------------------------------

# Build and start the docker-compose stack.
up:
    docker compose up --build -d

# Stop the stack.
stop:
    docker compose stop
