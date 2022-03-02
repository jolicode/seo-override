.DEFAULT_GOAL := help

cs: ## Fix CS with php-cs-fixer
	./vendor/bin/php-cs-fixer fix --verbose

cs_dry_run: ## Dry run of php-cs-fixer
	./vendor/bin/php-cs-fixer fix --verbose --dry-run

test: ## Launch tests suite
	./vendor/bin/phpunit

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
