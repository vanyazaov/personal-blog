#!/usr/bin/env bash
# Генерация TLS-сертификата для локальной разработки.
# Если установлен mkcert — генерирует доверенный сертификат (без warning'ов в браузере).
# Иначе — fallback на openssl self-signed (браузер ругнётся, но HTTPS работает).
set -euo pipefail

CERT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/certs"
mkdir -p "$CERT_DIR"

CERT="$CERT_DIR/localhost.pem"
KEY="$CERT_DIR/localhost-key.pem"

if [[ "${FORCE:-0}" != "1" && -f "$CERT" && -f "$KEY" ]]; then
    echo "✓ Сертификаты уже есть в $CERT_DIR (FORCE=1 чтобы перегенерировать)"
    exit 0
fi

if command -v mkcert >/dev/null 2>&1; then
    echo "→ Генерирую через mkcert (доверенный сертификат)"
    mkcert -install >/dev/null 2>&1 || true
    mkcert -cert-file "$CERT" -key-file "$KEY" localhost 127.0.0.1 ::1
    echo "✓ mkcert: сертификат создан и доверен системой"
else
    echo "→ mkcert не найден — использую openssl (self-signed)"
    echo "  Для доверенного локального TLS установи mkcert:"
    echo "    sudo apt install libnss3-tools && \\"
    echo "    curl -JLO https://dl.filippo.io/mkcert/latest?for=linux/amd64 && \\"
    echo "    sudo install mkcert-* /usr/local/bin/mkcert && rm mkcert-*"
    echo
    openssl req -x509 -nodes -newkey rsa:2048 -days 825 \
        -keyout "$KEY" -out "$CERT" \
        -subj "/CN=localhost/O=Dev/OU=Local" \
        -addext "subjectAltName=DNS:localhost,DNS:*.localhost,IP:127.0.0.1,IP:0.0.0.0,IP:::1" \
        -addext "extendedKeyUsage=serverAuth" \
        2>/dev/null
    echo "✓ openssl: self-signed сертификат создан"
fi

chmod 644 "$CERT"
chmod 600 "$KEY"
echo "  cert: $CERT"
echo "  key:  $KEY"
