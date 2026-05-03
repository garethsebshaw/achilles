#!/bin/bash

# Load environment variables from .env
if [ -f .env ]; then
export $(cat .env | grep -v '#' | awk '/^[A-Z]/ {print}')
else
echo ".env file not found"
exit 1
fi

clear

# Export current sessions
echo "*********************"
echo "Exporting current sessions..."
mysqldump -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" sessions > sessions_backup.sql
echo "Exported..."
echo "*********************"

# Refresh database
echo "Refreshing database..."
php artisan migrate:fresh
echo " "

# Import sessions back
echo "*********************"
echo "Restoring sessions..."
mysql -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < sessions_backup.sql
echo "Restored..."
echo "*********************"

echo " "

php artisan cache:clear
php artisan config:clear
#php artisan route:clear
#php artisan view:clear
#php artisan view:cache
php artisan optimize:clear

# Run seeders
echo "Running seeders..."
php artisan db:seed
echo " "


# Cleanup
rm sessions_backup.sql

echo "Database refresh complete!"
