.PHONY: test test-coverage analyse lint fix install

install:
	composer install

test:
	vendor/bin/phpunit

test-coverage:
	vendor/bin/phpunit --coverage-html coverage

analyse:
	vendor/bin/phpstan analyse --no-progress

lint:
	vendor/bin/php-cs-fixer fix --dry-run --diff

fix:
	vendor/bin/php-cs-fixer fix
