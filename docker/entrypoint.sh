#!/bin/bash
set -e

# Initialize public files volume if it's empty
if [ -d "/var/www/public" ]; then
    PUBLIC_VOLUME_FILES=$(find /var/www/public -mindepth 1 -maxdepth 1 2>/dev/null | wc -l)
    
    if [ "$PUBLIC_VOLUME_FILES" -eq 0 ]; then
        echo "Initializing public files volume..."
        
        # Copy built public files from backup to volume
        if [ -d "/var/www/public_backup" ]; then
            cp -r /var/www/public_backup/* /var/www/public/ 2>/dev/null || true
            echo "Public files copied from backup"
        fi
    fi
fi

# Initialize storage directories if they don't exist
mkdir -p /var/www/storage/app/public /var/www/storage/logs /var/www/storage/framework/cache /var/www/storage/framework/sessions /var/www/storage/framework/views

# Initialize storage symlink if it doesn't exist
if [ ! -L "/var/www/public/storage" ] && [ -d "/var/www/storage/app/public" ]; then
    echo "Creating storage symlink..."
    ln -sf /var/www/storage/app/public /var/www/public/storage
fi

# Set proper ownership for volumes
chown -R www-data:www-data /var/www/storage /var/www/public 2>/dev/null || true
chmod -R 775 /var/www/storage 2>/dev/null || true  
chmod -R 755 /var/www/public 2>/dev/null || true

echo "Container initialization complete"

# Execute the main command
exec "$@"