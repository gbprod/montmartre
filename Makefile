.PHONY=publish

install:
	composer install

publish:
	composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --no-suggest --optimize-autoloader --ignore-platform-reqs
	./publish.sh

publish-dev:
	./publish.sh dev
