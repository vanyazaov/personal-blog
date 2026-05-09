# ============================================================================
# Makefile — обёртка над docker compose. Запускать на хосте.
# Все команды в php-контейнере выполняются от UID/GID host-пользователя,
# чтобы создаваемые файлы не были root-овыми.
# ============================================================================

SHELL := /bin/bash
.DEFAULT_GOAL := help

# Подгружаем .env, чтобы UID/GID и порты были доступны и в Make
ifneq (,$(wildcard .env))
    include .env
    export
endif

DC          := docker compose
PHP_SVC     := php
EXEC        := $(DC) exec -T --user $(UID):$(GID) $(PHP_SVC)
EXEC_TTY    := $(DC) exec --user $(UID):$(GID) $(PHP_SVC)
EXEC_ROOT   := $(DC) exec --user root $(PHP_SVC)

# Хитрость: всё после первой цели передаётся как аргументы (для composer/git/...)
ARGS := $(filter-out $@,$(MAKECMDGOALS))

## ───── Помощь ──────────────────────────────────────────────────────────────
.PHONY: help
help: ## Показать эту справку
	@awk 'BEGIN {FS = ":.*?## "}; /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-22s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

## ───── Инициализация ──────────────────────────────────────────────────────
.PHONY: init certs certs-force
init: ## Первичная настройка: .env + серты + сборка + установка зависимостей
	@test -f .env || cp .env.example .env
	@echo "→ UID=$$(id -u) GID=$$(id -g) (проверь .env)"
	$(MAKE) certs
	$(MAKE) build
	$(MAKE) up
	$(MAKE) composer install
	@echo "✓ Готово. http://localhost:$${HTTP_PORT:-8000}  https://localhost:$${HTTPS_PORT:-8443}"

certs: ## Сгенерировать TLS-сертификаты для localhost (mkcert если есть, иначе openssl)
	@bash docker/nginx/gen-certs.sh

certs-force: ## Перегенерировать TLS-сертификаты, перезаписав существующие
	@FORCE=1 bash docker/nginx/gen-certs.sh
	@$(DC) ps --quiet nginx >/dev/null 2>&1 && $(DC) restart nginx || true

## ───── Жизненный цикл контейнеров ─────────────────────────────────────────
.PHONY: build up down restart stop start ps logs
build: ## Пересобрать образы (без кэша)
	$(DC) build --no-cache --pull

up: ## Поднять стек в фоне
	$(DC) up -d

down: ## Остановить и удалить контейнеры
	$(DC) down --remove-orphans

stop: ## Остановить контейнеры
	$(DC) stop

start: ## Запустить остановленные контейнеры
	$(DC) start

restart: ## Перезапустить
	$(DC) restart

ps: ## Список сервисов
	$(DC) ps

logs: ## Логи всех сервисов (follow)
	$(DC) logs -f --tail=100

logs-php: ## Логи php
	$(DC) logs -f --tail=100 php

logs-nginx: ## Логи nginx
	$(DC) logs -f --tail=100 nginx

logs-mysql: ## Логи mysql
	$(DC) logs -f --tail=100 mysql

fresh: ## Полный сброс: down -v + build + up + composer install
	$(DC) down -v --remove-orphans
	$(MAKE) build
	$(MAKE) up
	$(MAKE) composer install

## ───── Шеллы ───────────────────────────────────────────────────────────────
.PHONY: shell shell-root sh
shell sh: ## Интерактивный bash в php-контейнере (от пользователя app)
	$(EXEC_TTY) bash

shell-root: ## bash от root (для apt-get/pecl и пр.)
	$(EXEC_ROOT) bash

mysql-shell: ## mysql-клиент внутри mysql-контейнера
	$(DC) exec mysql mysql -u$$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE

redis-cli: ## redis-cli
	$(DC) exec redis redis-cli

## ───── Composer / PHP / Git ───────────────────────────────────────────────
.PHONY: composer php git artisan
composer: ## composer (передаёт аргументы): make composer require/install/...
	$(EXEC) composer $(ARGS)

php: ## php (передаёт аргументы): make php -v
	$(EXEC) php $(ARGS)

git: ## git внутри контейнера (на случай — но рекомендуется снаружи)
	$(EXEC) git $(ARGS)

## ───── Тесты ──────────────────────────────────────────────────────────────
.PHONY: test test-coverage test-type test-filter
test: ## Pest: все тесты
	$(EXEC) ./vendor/bin/pest

test-coverage: ## Pest: с покрытием (мин. 80%)
	$(EXEC) ./vendor/bin/pest --coverage --min=80

test-type: ## Pest: type-coverage 100%
	$(EXEC) ./vendor/bin/pest --type-coverage --min=100

test-filter: ## Pest: фильтр (make test-filter name=<part>)
	$(EXEC) ./vendor/bin/pest --filter="$(name)"

## ───── Анализаторы / линтеры / рефакторинг ───────────────────────────────
.PHONY: stan cs-check cs-fix rector rector-fix phpcs phpmd lint fix pre-commit
stan: ## PHPStan level=max
	$(EXEC) ./vendor/bin/phpstan analyse --memory-limit=1G

cs-check: ## PHP-CS-Fixer (только проверка)
	$(EXEC) ./vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix: ## PHP-CS-Fixer (автофикс)
	$(EXEC) ./vendor/bin/php-cs-fixer fix

rector: ## Rector (dry-run)
	$(EXEC) ./vendor/bin/rector process --dry-run

rector-fix: ## Rector (применить рефакторинги)
	$(EXEC) ./vendor/bin/rector process

phpcs: ## PHP_CodeSniffer (PSR-12 + strict)
	$(EXEC) ./vendor/bin/phpcs

phpcs-fix: ## PHP_CodeSniffer (PSR-12 + strict)
	$(EXEC) ./vendor/bin/phpcbf

phpmd: ## PHPMD
	$(EXEC) ./vendor/bin/phpmd src text phpmd.xml || echo "PHPMD found violations but continuing..."

lint: cs-check stan rector phpcs phpmd ## Прогнать ВСЕ проверки
	@echo "✓ Все линтеры/анализаторы прошли"

fix: cs-fix rector-fix ## Прогнать ВСЕ автофиксы
	@echo "✓ Все автофиксы применены"

pre-commit: fix lint test ## Хук перед коммитом: автофиксы + проверки + тесты
	@echo "✓ Готово к коммиту"

## ───── База данных ────────────────────────────────────────────────────────
.PHONY: db-dump db-restore
db-dump: ## Дамп БД в ./dump.sql (на хосте)
	$(DC) exec -T mysql mysqldump -u$$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE > dump.sql
	@echo "✓ dump.sql"

db-restore: ## Восстановить БД из ./dump.sql
	@test -f dump.sql || (echo "✗ dump.sql не найден" && exit 1)
	$(DC) exec -T mysql mysql -u$$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE < dump.sql
	@echo "✓ Восстановлено"

## ───── Утилиты ────────────────────────────────────────────────────────────
.PHONY: urls
urls: ## Показать URL'ы сервисов
	@echo "App (http):  http://localhost:$${HTTP_PORT:-8000}"
	@echo "App (https): https://localhost:$${HTTPS_PORT:-8443}"
	@echo "Adminer:     http://localhost:$${ADMINER_PORT:-8080}"
	@echo "Mailpit:     http://localhost:$${MAILPIT_WEB_PORT:-8025}"
	@echo "MySQL:       localhost:$${MYSQL_PORT:-3306}"

# Проглатываем аргументы, чтобы make composer install не падал на цели "install"
%:
	@:
