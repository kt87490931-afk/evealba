#!/bin/bash
# Pool config에서 키 추출 후 gemini_api_key.env 복원 (크론용)
set -e
POOL_CONF="/etc/php/8.3/fpm/pool.d/www.conf"
KEY_FILE="/var/www/evealba/extend/gemini_api_key.env"
KEY=$(grep '^env\[GEMINI_API_KEY\]' "$POOL_CONF" | sed 's/.*= *"\(.*\)".*/\1/' | tr -d '\n\r')
if [ -n "$KEY" ]; then
  echo -n "$KEY" > "$KEY_FILE"
  chown www-data:www-data "$KEY_FILE"
  chmod 600 "$KEY_FILE"
  echo "Restored key to gemini_api_key.env for cron"
else
  echo "No key in pool config"
  exit 1
fi
