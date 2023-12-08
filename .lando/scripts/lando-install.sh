#!/bin/bash

lando composer install

if [ -f "reference/sanitized.sql" ]
  then
    echo "Reference database found. Importing..."
    lando db-import reference/sanitized.sql
    if ! [ -f "web/sites/default/settings.php" ]
      then
        echo "Generating settings.php file..."
        cp web/sites/default/default.settings.php web/sites/default/settings.php
    fi
  else
    echo "No reference database found. Running site-install..."
    lando drush site-install -y
fi

cd web

#FIXME: .htaccess breaks this check
	if [ "$(ls -A ../config/sync)" ]; then
     lando drush cim -y
	else
    echo "config/sync is empty; not running import"
	fi


lando drush cr

lando drush user:password admin "admin"
lando drush sql-query "UPDATE users_field_data SET name='admin' WHERE uid=1";
lando drush user:password admin "admin"
