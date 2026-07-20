#!/usr/bin/env bash

set -euo pipefail

PROJECT_DIR="${PROJECT_DIR:-$HOME/Documents/sharkdevelop-wordpress}"
LOCAL_PUBLIC="${LOCAL_PUBLIC:-/Users/olgastennikova/Local Sites/sharkdevelop/app/public}"
LOCAL_URL_HTTP="${LOCAL_URL_HTTP:-http://sharkdevelop.local}"
LOCAL_URL_HTTPS="${LOCAL_URL_HTTPS:-https://sharkdevelop.local}"

SSH_HOST="${SSH_HOST:-213.130.145.225}"
SSH_PORT="${SSH_PORT:-65002}"
SSH_USER="${SSH_USER:-u781562985}"

DEV_URL="${DEV_URL:-https://dev.sharkdevelop.com}"
DEV_PATH="${DEV_PATH:-/home/u781562985/domains/sharkdevelop.com/public_html/dev}"
DEV_DB="${DEV_DB:-u781562985_devsite}"

DUMP_FILE="${DUMP_FILE:-$PROJECT_DIR/.dev-deploy.sql}"
REMOTE_DUMP="${REMOTE_DUMP:-/home/$SSH_USER/dev-deploy.sql}"
DELETE_REMOTE_UPLOADS="${DELETE_REMOTE_UPLOADS:-0}"

ENV_FILE="$PROJECT_DIR/scripts/deploy-dev.local.env"
if [[ -f "$ENV_FILE" ]]; then
  # shellcheck disable=SC1090
  source "$ENV_FILE"
fi

LOCAL_APP_DIR="$(dirname "$LOCAL_PUBLIC")"
LOCAL_ENVRC="$LOCAL_APP_DIR/.envrc"
if [[ -f "$LOCAL_ENVRC" ]]; then
  # Local generates this file with the right PHP, MySQL and WP-CLI paths.
  # shellcheck disable=SC1090
  source "$LOCAL_ENVRC"
fi

cleanup() {
  rm -f "$DUMP_FILE"
}
trap cleanup EXIT

fail() {
  echo "Ошибка: $*" >&2
  exit 1
}

require_command() {
  command -v "$1" >/dev/null 2>&1 || fail "не найдена команда '$1'"
}

resolve_php() {
  if [[ -n "${PHP_BIN:-}" ]]; then
    [[ -x "$PHP_BIN" ]] || fail "PHP_BIN не запускается: $PHP_BIN"
    return
  fi

  if command -v php >/dev/null 2>&1; then
    PHP_BIN="$(command -v php)"
    return
  fi

  PHP_BIN="$(find "$HOME/Library/Application Support/Local/lightning-services" -type f -perm -111 -name php 2>/dev/null | head -n 1 || true)"
  if [[ -n "$PHP_BIN" ]]; then
    return
  fi

  PHP_BIN="$(find "/Applications/Local.app/Contents/Resources/extraResources/lightning-services" -type f -perm -111 -name php 2>/dev/null | head -n 1 || true)"
  if [[ -n "$PHP_BIN" ]]; then
    return
  fi

  fail "PHP не найден. Укажи PHP_BIN в scripts/deploy-dev.local.env"
}

resolve_wp_cli() {
  if [[ -n "${WP_CLI_COMMAND:-}" ]]; then
    read -r -a WP_CLI <<< "$WP_CLI_COMMAND"
    return
  fi

  if command -v wp >/dev/null 2>&1; then
    WP_CLI=(wp)
    return
  fi

  if [[ -f "$PROJECT_DIR/scripts/wp-cli.phar" ]]; then
    resolve_php
    WP_CLI=("$PHP_BIN" "$PROJECT_DIR/scripts/wp-cli.phar")
    return
  fi

  if [[ -f "$PROJECT_DIR/wp-cli.phar" ]]; then
    resolve_php
    WP_CLI=("$PHP_BIN" "$PROJECT_DIR/wp-cli.phar")
    return
  fi

  fail "WP-CLI не найден. Установи wp-cli, положи wp-cli.phar в scripts/ или укажи WP_CLI_COMMAND в scripts/deploy-dev.local.env"
}

cd "$PROJECT_DIR"

echo "1. Проверяем окружение..."
[[ -d "$PROJECT_DIR" ]] || fail "PROJECT_DIR не существует: $PROJECT_DIR"
[[ -d "$LOCAL_PUBLIC" ]] || fail "LOCAL_PUBLIC не существует: $LOCAL_PUBLIC"
[[ -d "$LOCAL_PUBLIC/wp-content/uploads" ]] || fail "uploads не найден: $LOCAL_PUBLIC/wp-content/uploads"
require_command git
require_command rsync
require_command scp
require_command ssh
resolve_wp_cli

CURRENT_BRANCH="$(git branch --show-current)"
[[ "$CURRENT_BRANCH" == "dev" ]] || fail "сейчас активна ветка '$CURRENT_BRANCH'. Переключись на dev перед деплоем"

if [[ -n "$(git status --porcelain)" ]]; then
  fail "есть незакоммиченные изменения. Сначала сделай commit"
fi

echo "2. Проверяем доступ к dev-серверу..."
ssh -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "cd '$DEV_PATH' && test -f wp-config.php && command -v wp >/dev/null"

echo "3. Отправляем ветку dev..."
git push origin dev
ssh -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "cd '$DEV_PATH' && git pull origin dev && wp plugin list >/dev/null"

echo "4. Экспортируем локальную базу..."
"${WP_CLI[@]}" --path="$LOCAL_PUBLIC" db export "$DUMP_FILE"

echo "5. Синхронизируем uploads..."
if [[ "$DELETE_REMOTE_UPLOADS" == "1" ]]; then
  rsync -az --progress --delete \
    -e "ssh -p $SSH_PORT" \
    "$LOCAL_PUBLIC/wp-content/uploads/" \
    "$SSH_USER@$SSH_HOST:$DEV_PATH/wp-content/uploads/"
else
  rsync -az --progress \
    -e "ssh -p $SSH_PORT" \
    "$LOCAL_PUBLIC/wp-content/uploads/" \
    "$SSH_USER@$SSH_HOST:$DEV_PATH/wp-content/uploads/"
fi

echo "6. Загружаем дамп..."
scp -P "$SSH_PORT" \
  "$DUMP_FILE" \
  "$SSH_USER@$SSH_HOST:$REMOTE_DUMP"

LOCAL_URL_HTTP_ESC="${LOCAL_URL_HTTP//\//\\/}"
LOCAL_URL_HTTPS_ESC="${LOCAL_URL_HTTPS//\//\\/}"
DEV_URL_ESC="${DEV_URL//\//\\/}"

echo "7. Импортируем базу и заменяем адрес..."
ssh -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" <<EOF
set -e

cd "$DEV_PATH"

mysql "$DEV_DB" < "$REMOTE_DUMP"

wp search-replace \
  '$LOCAL_URL_HTTP' \
  '$DEV_URL' \
  --all-tables \
  --report-changed-only

wp search-replace \
  '$LOCAL_URL_HTTPS' \
  '$DEV_URL' \
  --all-tables \
  --report-changed-only

wp search-replace \
  '$LOCAL_URL_HTTP_ESC' \
  '$DEV_URL_ESC' \
  --all-tables \
  --report-changed-only

wp search-replace \
  '$LOCAL_URL_HTTPS_ESC' \
  '$DEV_URL_ESC' \
  --all-tables \
  --report-changed-only

wp option update home '$DEV_URL'
wp option update siteurl '$DEV_URL'
wp option update blog_public 0

wp plugin deactivate wps-hide-login 2>/dev/null || true
wp cache flush
wp rewrite flush
wp elementor flush_css || true

rm -f "$REMOTE_DUMP"
EOF

echo "Готово: $DEV_URL"
