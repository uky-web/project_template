#!/usr/bin/env bash
set -ex

wait_for_docker() {
  while true; do
    docker ps > /dev/null 2>&1 && break
    sleep 1
  done
  echo "Docker is ready."
}

wait_for_docker

# Download images beforehand (optional)
ddev debug download-images

# Avoid errors on rebuilds
ddev poweroff

# Configure DDEV
ddev config global --omit-containers=ddev-router
ddev config --auto

# Start DDEV project automatically
ddev start -y

# Configure Composer plugins
ddev composer global config allow-plugins true
ddev composer config allow-plugins.composer/installers true
ddev composer config allow-plugins.drupal/core-composer-scaffold true
ddev composer config allow-plugins.cweagans/composer-patches true
ddev composer config allow-plugins.zaporylie/composer-drupal-optimizations true
ddev composer config allow-plugins.drupal/console-extend-plugin true
ddev composer config allow-plugins.oomphinc/composer-installers-extender true

# Install dependencies
ddev composer install

# Run additional setup script
.ddev/uk-site-install.sh

# Show DDEV Drush status
ddev drush status
