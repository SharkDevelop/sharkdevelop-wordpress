#!/usr/bin/env bash

set -euo pipefail

PROJECT_DIR="$HOME/Documents/sharkdevelop-wordpress"
LOCAL_PUBLIC="/Users/olgastennikova/Local Sites/sharkdevelop/app/public"

SSH_HOST="213.130.145.225"
SSH_PORT="65002"
SSH_USER="u781562985"

DEV_PATH="/home/u781562985/domains/sharkdevelop.com/public_html/dev"
DEV_DB="u781562985_devsite"

DUMP_FILE="$PROJECT_DIR/.dev-deploy.sql"
REMOTE_DUMP="/home/$SSH_USER/dev-deploy.sql"

cd "$PROJECT_DIR"

echo "1. Проверяем Git..."
if [[ -n "$(git status --porcelain)" ]]; then
  echo "Есть незакоммиченные изменения. Сначала сделай commit."
  exit 1
fi

echo "2. Отправляем ветку dev..."
git push origin dev

echo "3. Экспортируем локальную базу..."
cd "$LOCAL_PUBLIC"
wp db export "$DUMP_FILE"

echo "4. Синхронизируем uploads..."
rsync -az --progress \
  -e "ssh -p $SSH_PORT" \
  "$PROJECT_DIR/wp-content/uploads/" \
  "$SSH_USER@$SSH_HOST:$DEV_PATH/wp-content/uploads/"

echo "5. Загружаем дамп..."
scp -P "$SSH_PORT" \
  "$DUMP_FILE" \
  "$SSH_USER@$SSH_HOST:$REMOTE_DUMP"

echo "6. Импортируем базу и заменяем адрес..."
ssh -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" <<EOF
set -e

cd "$DEV_PATH"

mysql "$DEV_DB" < "$REMOTE_DUMP"

wp search-replace \
  'http://sharkdevelop.local' \
  'https://dev.sharkdevelop.com' \
  --all-tables

wp search-replace \
  'https://sharkdevelop.local' \
  'https://dev.sharkdevelop.com' \
  --all-tables

wp option update home 'https://dev.sharkdevelop.com'
wp option update siteurl 'https://dev.sharkdevelop.com'
wp option update blog_public 0

wp plugin deactivate wps-hide-login 2>/dev/null || true
wp cache flush
wp rewrite flush

rm -f "$REMOTE_DUMP"
EOF

rm -f "$DUMP_FILE"

echo "Готово: https://dev.sharkdevelop.com"