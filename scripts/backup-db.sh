#!/bin/bash
# ============================================
# Database Backup Script for Architect AI
# ============================================
# Creates timestamped backups
# Organizes by: backups/YYYY-MM/YYYY-MM-DD_HH-mm-ss.sql.gz
# Keeps last 7 days of backups
# ============================================
# Usage: Run from host with: bash scripts/backup-db.sh
# ============================================

set -e

# Configuration
BACKUP_DIR="./backups"
DB_NAME="architect_ai"
DB_USER="root"
DB_PASS="archpass123"
DB_CONTAINER="architect-ai-db"
DAYS_TO_KEEP=7

# Create backup directory with date structure
DATE_DIR=$(date +%Y-%m)
TIMESTAMP=$(date +%Y-%m-%d_%H-%M-%S)
FULL_BACKUP_DIR="${BACKUP_DIR}/${DATE_DIR}"
BACKUP_FILE="${FULL_BACKUP_DIR}/${TIMESTAMP}.sql"

# Create directories
mkdir -p "${FULL_BACKUP_DIR}"

# Run backup from db container
echo "Backing up database ${DB_NAME} to ${BACKUP_FILE}..."
docker exec ${DB_CONTAINER} mysqldump -u${DB_USER} -p${DB_PASS} ${DB_NAME} > "${BACKUP_FILE}"

# Verify backup
if [ -f "${BACKUP_FILE}" ] && [ -s "${BACKUP_FILE}" ]; then
    echo "Backup successful: ${BACKUP_FILE}"
    echo "Size: $(du -h ${BACKUP_FILE} | cut -f1)"
else
    echo "ERROR: Backup failed!"
    exit 1
fi

# Cleanup old backups (keep last 7 days)
echo "Cleaning up backups older than ${DAYS_TO_KEEP} days..."
find ${BACKUP_DIR} -name "*.sql" -mtime +${DAYS_TO_KEEP} -delete
echo "Cleanup complete."

echo "Backup process finished."
