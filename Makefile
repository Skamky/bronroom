.PHONY: up
up:
	./vendor/bin/sail up -d
	./vendor/bin/sail npm run dev -d

.PHONY: down
down:
	./vendor/bin/sail down

