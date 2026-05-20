.PHONY: up
up:
	./vendor/bin/sail up -d
	./vendor/bin/sail npm run dev -d

.PHONY: down
down:
	./vendor/bin/sail down

.PHONY: build
build:
	@echo "🔨 Сборка фронтенда..."
	./vendor/bin/sail npm run build
	@echo "✅ Сборка завершена"

.PHONY: release
release: build
	@echo "📦 Очистка кэша Laravel..."
	./vendor/bin/sail artisan config:clear
	./vendor/bin/sail artisan cache:clear
	./vendor/bin/sail artisan view:clear
	./vendor/bin/sail artisan route:clear
	@echo "🗜️ Создание архива..."
	@VERSION=$$(git describe --tags --always --dirty); \
	zip -r ../laravel-release-$$VERSION.zip . \
	-x "*.git*" \
	-x "node_modules/*" \
	-x ".env" \
	-x "storage/logs/*" \
	-x "storage/framework/sessions/*" \
	-x "storage/framework/cache/*" \
	-x "storage/framework/views/*" \
	-x "bootstrap/cache/*" \
	-x "*.log" \
	-x "tests/*" \
	-x "vendor/*" \
	-x ".DS_Store" \
	-x "code-generator/*" \
	-x "uml/*"
	@echo "✅ Архив создан: ../laravel-release-$$VERSION.zip"
	@echo "⚠️  На сервере выполните: composer install --no-dev --optimize-autoloader && php artisan migrate --force"

.PHONY: dev
dev:
	@echo "🔄 Установка dev-зависимостей..."
	./vendor/bin/sail composer install
	@echo "✅ Готово к разработке"
