#!/bin/bash
# ============================================
# Database Restore Script for Architect AI
# ============================================
# Usage: bash scripts/restore-db.sh backups/2026-04/2026-04-13_10-30-00.sql
# ============================================

set -e

if [ -z "$1" ]; then
    echo "Usage: $0 <backup_file>"
    echo "Example: $0 ./backups/2026-04/2026-04-13_10-30-00.sql"
    exit 1
fi

BACKUP_FILE="$1"
DB_NAME="architect_ai"
DB_USER="root"
DB_PASS="archpass123"
DB_CONTAINER="architect-ai-db"

if [ ! -f "${BACKUP_FILE}" ]; then
    echo "ERROR: Backup file not found: ${BACKUP_FILE}"
    exit 1
fi

# Confirmation prompt
echo "WARNING: This will overwrite the current database '${DB_NAME}'!"
echo "Backup file: ${BACKUP_FILE}"
read -p "Are you sure you want to continue? (yes/no): " CONFIRM

if [ "${CONFIRM}" != "yes" ]; then
    echo "Restore cancelled."
    exit 0
fi

echo "Restoring database..."
docker exec -i ${DB_CONTAINER} mysql -u${DB_USER} -p${DB_PASS} ${DB_NAME} < "${BACKUP_FILE}"

echo "Restore complete."
