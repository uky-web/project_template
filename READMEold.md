# UKY Web Platform Project Template

This project template provides a starter kit for managing your site dependencies with [Composer](). It is a variation of the official [Drupal Composer](https://github.com/drupal-composer/drupal-project) Drupal project starter kit, with additions and modifications specific to the Univeristy of Kentucky web platform. 

## UK Specific Modifications

This version of the Drupal Project makes the following modifications to the default drupal-composer project

* Adds the UK composer repository (satis.uky.dev)
* Adds the UK Installation Profile as a requirement
* Removes Drupal Core as a requirement (it is managed by the Installation Profile)
* Adds additional installer types for npm, bower, and custom drupal themes, modules, and profiles. 
* Adds specific installer paths for those new installer types.
* Runs npm install specifically for the UK base theme.
* Provides a default.settings.php that enables local settings files, and loads database configuration values from environment variables.
* Provides default database connection settings that correspond to the `mysql` service available in the [Dev Container](https://gitlab.com/uky-web/university-web-platform/drupal-8/devcontainer).
* Provides the [UKY Platform Dev Tools](https://gitlab.com/uky-web/university-web-platform/drupal-8/uky_platform_dev_tools) package which has default configuration for using [gitpod.io](https://gitpod.io) / [ddev](https://ddev.readthedocs.io/en/stable/) for development or [lando](https://docs.lando.dev/config/drupal9.html) for local development.

When installing the given `composer.json` some tasks are taken care of:

* The UK base Installation Profile is installed, and along with it the UK base theme, as well as Drupal core.
* Autoloader is implemented to use the generated composer autoloader in `vendor/autoload.php`,
  instead of the one provided by Drupal (`web/vendor/autoload.php`).
* Contributed modules (packages of type `drupal-module`) will be placed in `web/modules/contrib/`.
* Custom modules (packages type `drupal-custom-module`) will be placed in `web/modules/custom`.
* Contributed themes (packages of type `drupal-theme`) will be placed in `web/themes/contrib/`.
* Custom themes (packages of type `drupal-custom-theme`) will be placed in `web/themes/custom/`.
* Contributed profiles (packages of type `drupal-profile`) will be placed in `web/profiles/contrib/`.
* Custom profiles (packages of type `drupal-custom-profile`) will be placed in `web/profiles/custom/`.
* Creates default writable versions of `settings.php` and `services.yml`.
* Creates `web/sites/default/files`-directory.
* Latest version of drush is installed locally for use at `vendor/bin/drush`.
* Latest version of DrupalConsole is installed locally for use at `vendor/bin/drupal`.
* Creates environment variables based on your .env file. See [.env.example](.env.example).


## Managing Drupal Core
If your site project uses the UK installation profie, it's recommended to allow the installation profile to define the Drupal Core dependency. If you use this project template without the UK installation profile, you will need to require Drupal Core in the projec template's composer.json file. See below for additional considerations.

### Scaffolding Files
Some files managed in the Drupal Core project need to be installed outside of that package's directory, in the root of your site. This is accomplished with the `drupal/core-composer-scaffold` package. This package should be required alongside Drupal Core, and is required by the installation profile. If you remove the installation profile from this projec template, or add Drupal Core, you should also require the core-composer-scaffold package.

### Troubleshooting
USE WITH CAUTION: If you run into any enviroment or container related issues, where it seems that there is a enviroment setting is cached, attempt these steps to get the project up and running:
1. `ddev poweroff`
2. `docker rm -f $(docker ps -aq) # stop any other random containers that may be running`
3. `docker rmi -f $(docker images -q) # You might have to repeat this a time or two to get rid of all images`
4. Completely stop and restart the gitpod workspace.

## FAQ

### What do I commit into my project's repository?
After installing via `composer install`, you should commit the resulting files as the included `.gitignore` is configured. 

### Should I commit the contrib and custom modules I download?
No. Your project should maintain its dependencies (such as additional modules or themes) using composer. 

###
