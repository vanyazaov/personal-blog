# Test Task — Dev Environment

PHP 8.3 + Smarty 5 + MySQL 8 + Nginx + Redis + Mailpit + Adminer.
Полностью изолирован в Docker. Все инструменты разработки установлены через composer (`require --dev`) и запускаются внутри контейнера.

## Стек

| Сервис   | Образ / Версия         | Где открыть                                      |
|----------|------------------------|--------------------------------------------------|
| Nginx    | `nginx:1.27-alpine`    | `http://localhost:8000` · `https://localhost:8443` |
| PHP-FPM  | `php:8.3-fpm-bookworm` | (внутренне)                                      |
| MySQL    | `mysql:8.0`            | `localhost:3306`                                 |
| Redis    | `redis:7-alpine`       | (внутренне; `make redis-cli`)                    |
| Adminer  | `adminer:latest`       | `http://localhost:8080`                          |
| Mailpit  | `axllent/mailpit`      | `http://localhost:8025` (SMTP 1025)              |

PHP-расширения: pdo_mysql, mysqli, mbstring, zip, exif, pcntl, bcmath, gd, intl, opcache, redis, xdebug.

## Быстрый старт

```bash
cp .env.example .env
# проверь UID=$(id -u) и GID=$(id -g) в .env
make init        # build + up + composer install
make urls        # покажет адреса сервисов
```

После `composer install` (он подтянет Smarty 5 и dev-инструменты):

```bash
make lint        # cs-check + phpstan(max) + rector(dry) + phpcs + phpmd
make fix         # автофиксы: cs-fixer + rector
make test        # pest
make pre-commit  # fix + lint + test (запускать перед каждым git commit на хосте)
```

## Пользователь и права

UID/GID host-пользователя пробрасываются в Dockerfile (build-arg) и в `docker compose exec --user`. Файлы, создаваемые композером и инструментами, принадлежат твоему юзеру. Git выполняется снаружи проекта.

## Структура

```
test-task/
├── app/                      # ← bind-mount → /var/www, твой код тут
│   ├── public/index.php      # точка входа (smoke-test)
│   ├── src/                  # PSR-4: App\
│   ├── tests/{Unit,Feature}/ # Pest
│   ├── composer.json         # включая dev-инструменты на max strict
│   ├── phpstan.neon          # level: max + strict-rules
│   ├── .php-cs-fixer.php     # PER-CS 2.0 + PHP83 + strict
│   ├── rector.php            # все «жёсткие» наборы
│   ├── phpcs.xml             # PSR-12 + RequireStrictTypes
│   ├── phpmd.xml             # cleancode/codesize/design/...
│   └── pest.xml              # failOnRisky/Warning/Notice/Deprecation
├── docker/
│   ├── php/{Dockerfile,php.ini,xdebug.ini}
│   ├── nginx/{nginx.conf,default.conf}
│   └── mysql/my.cnf          # STRICT_ALL_TABLES + ONLY_FULL_GROUP_BY
├── docker-compose.yml
├── Makefile
├── .env.example
├── .editorconfig
└── .gitignore
```

## Полезные команды Makefile

| Команда             | Что делает                                                        |
|---------------------|-------------------------------------------------------------------|
| `make help`         | Справка по всем целям                                             |
| `make up/down/ps`   | Подъём/остановка/статус                                           |
| `make shell`        | bash в php-контейнере (от твоего UID)                             |
| `make composer ...` | Прокидывает аргументы: `make composer require vendor/pkg`         |
| `make php ...`      | Прокидывает в `php`: `make php -v`                                |
| `make test`         | Pest                                                              |
| `make lint` / `fix` | Все анализаторы / все автофиксеры                                 |
| `make pre-commit`   | fix + lint + test (ручной хук перед коммитом, git у тебя снаружи) |
| `make db-dump`      | Дамп БД в `dump.sql`                                              |
| `make fresh`        | Полный сброс (включая volumes)                                    |
| `make urls`         | Адреса всех сервисов                                              |

## HTTPS локально

Nginx слушает и `:80`, и `:443` (на хосте — `${HTTP_PORT}` и `${HTTPS_PORT}`, по умолчанию 8000 и 8443). Сертификаты лежат в [docker/nginx/certs/](docker/nginx/certs/) (gitignored), генерятся скриптом [docker/nginx/gen-certs.sh](docker/nginx/gen-certs.sh):

- **Если установлен `mkcert`** — создаётся доверенный сертификат (без ругани в браузере).
- **Иначе** — fallback на `openssl` self-signed (браузер покажет warning, ОК для dev).

`make init` уже вызывает `make certs`. Перегенерировать вручную: `make certs-force`.

Установка mkcert (рекомендую — без warning'ов):
```bash
sudo apt install libnss3-tools
curl -JLO https://dl.filippo.io/mkcert/latest?for=linux/amd64
sudo install mkcert-* /usr/local/bin/mkcert && rm mkcert-*
make certs-force   # перегенерировать с доверенным CA
```

TLS: только TLSv1.2/1.3, modern ciphers, `HTTP/2` включён. SAN включает `localhost`, `127.0.0.1`, `::1`. Если нужно добавить домен (например `app.local` через `/etc/hosts`) — допиши в `gen-certs.sh` и запусти `make certs-force`.

## Xdebug

По умолчанию `xdebug.mode=develop,debug,coverage`, `start_with_request=trigger`. Чтобы выключить — поставь `XDEBUG_MODE=off` в `.env` и `make restart`.
Слушает на `host.docker.internal:9003`. В IDE настрой server name = `docker`, path mapping `app/` ↔ `/var/www`.

## Mailpit (SMTP-заглушка)

Из приложения настрой почту на `mailpit:1025` (без auth/TLS). Все письма ловятся в `http://localhost:8025`.

## Adminer

`http://localhost:8080`. Сервер уже подставлен (`mysql`), логин/пароль/БД — из `.env`.
