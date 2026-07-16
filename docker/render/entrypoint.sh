#!/bin/sh
set -e

# Render injecte dynamiquement la variable PORT (souvent 10000).
# nginx doit écouter exactement dessus, sinon Render considère le service down.
PORT="${PORT:-10000}"
sed -i "s/__PORT__/${PORT}/g" /etc/nginx/sites-enabled/default

exec "$@"
