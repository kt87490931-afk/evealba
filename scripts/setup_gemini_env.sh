#!/bin/bash
# 서버에 GEMINI_API_KEY 환경변수 설정 (PHP-FPM pool)
# 사용법: ssh root@서버 'bash -s' < setup_gemini_env.sh

set -e
KEY_FILE="/var/www/evealba/extend/gemini_api_key.env"
POOL_CONF="/etc/php/8.3/fpm/pool.d/www.conf"
ENV_LINE="env[GEMINI_API_KEY]"

if [ ! -f "$KEY_FILE" ]; then
  echo "ERROR: $KEY_FILE not found"
  exit 1
fi
KEY=$(tr -d '\n\r' < "$KEY_FILE")

if grep -q "^${ENV_LINE}=" "$POOL_CONF" 2>/dev/null; then
  sed -i "s|^${ENV_LINE}=.*|${ENV_LINE} = \"${KEY}\"|" "$POOL_CONF"
  echo "Updated existing GEMINI_API_KEY in pool config"
else
  echo "" >> "$POOL_CONF"
  echo "; Gemini API (env for security)" >> "$POOL_CONF"
  echo "${ENV_LINE} = \"${KEY}\"" >> "$POOL_CONF"
  echo "Added GEMINI_API_KEY to pool config"
fi

# gemini_api_key.env 유지: CLI(크론)는 FPM env 없음 → 파일 fallback 필요
# 웹 요청은 FPM env 우선 사용. 파일은 .gitignore, chmod 600
chown www-data:www-data "$KEY_FILE"
chmod 600 "$KEY_FILE"
echo "gemini_api_key.env kept for cron/CLI (web uses FPM env)"

systemctl restart php8.3-fpm
echo "php8.3-fpm restarted - Done"
