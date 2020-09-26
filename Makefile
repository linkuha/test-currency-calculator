.DEFAULT_GOAL := hello

hello:
	@echo "Welcome to commands shortcuts"

tests-run:
	vendor/bin/phpunit --verbose ./tests

tests-run-coverage:
	vendor/bin/phpunit --coverage-html coverage --coverage-filter ./src ./tests

tests-run-calc:
	vendor/bin/phpunit --verbose ./tests/AverageRateCalculatorTest.php

tests-run-cbrf:
	vendor/bin/phpunit --verbose ./tests/CbrfRatesLoaderTest.php

tests-run-rbk:
	vendor/bin/phpunit --verbose ./tests/RbkRatesLoaderTest.php