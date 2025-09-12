#!/bin/sh
# Deployment script to set storage permissions
set -e

# Allow overriding user/group via environment variables
WEB_USER="${WEB_USER:-www-data}"
WEB_GROUP="${WEB_GROUP:-www-data}"

chown -R "$WEB_USER":"$WEB_GROUP" storage
chmod -R 750 storage