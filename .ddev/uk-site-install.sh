#!/bin/bash
set -e

# Import the reference DB
if [ -f "reference/sanitized.sql.gz" ]
  then
    echo "Reference database found. Importing..."
    # Unzip the reference DB
    gunzip reference/sanitized.sql.gz
    ddev import-db --src=reference/sanitized.sql
    # gzip the reference DB
    gzip reference/sanitized.sql
    # discard changes to gzip reference DB
    git checkout reference/sanitized.sql.gz
    if ! [ -f "web/sites/default/settings.php" ]
      then
        echo "Generating settings.php file..."
        cp web/sites/default/default.settings.php web/sites/default/settings.php
    fi
    chmod +x scripts/local_packages/local_packages.sh
    mv config/sync/stage_file_proxy.settings.yml config/
    ddev drush cr
    ddev drush cim -y
    ddev drush cr
    ddev drush en stage_file_proxy
    ddev drush cex -y
    mv config/stage_file_proxy.settings.yml config/sync/
    ddev drush cr
    ddev drush cim -y
    file="reference/.siteurl"
    siteurl=$(cat "$file")
    if [ -s "$file" ]; then
        ddev drush config-set stage_file_proxy.settings origin $siteurl -y
        ddev drush cr
        echo "Stage File Proxy enabled and configured..."
    else
        echo ".siteurl file is empty. Skipping configuration..."
    fi
    # discard changes to core.extension.yml so stage_file_proxy being enabled doesn't get committed.
    git checkout config/sync/core.extension.yml
    ddev drush cr
    ddev drush user:create admin --password="admin"
    ddev drush urol "administrator" admin
    ddev drush php-eval 'node_access_rebuild();'
  else
    echo "No reference database found. Running site-install..."
     ddev drush si -y --account-pass=admin --site-name='uky_base' uky_base
     ddev drush php-eval 'node_access_rebuild();'
fi
