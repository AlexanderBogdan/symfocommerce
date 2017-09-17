#!/bin/bash

# Update DB
#php app/console doctrine:schema:update --force
#echo -e "Database was updated successfully."


#php app/console doctrine:migrations:migrate
#echo -e "All migrations was executed."2

# Clear cache
rm -R var/cache/*
echo -e "Clearing the cache was successfully done."

# Update CSS, JS, Image styles
php bin/console assets:install web --symlink
echo -e "Symlinks were updated successfully."

# Installing CSS, JS and Image styles
#php bin/console assets:dump
#echo -e "Symlinks were installed successfully."

# Set needed permissions for app folders
chmod 777 -R var/cache/ var/logs/ var/sessions
echo -e "All post deploy procedures are finished successfully!"
