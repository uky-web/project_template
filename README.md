# UK Web Platform Site

This README serves as a guide for developing your [Drupal](https://www.drupal.org) site on the [University of Kentucky Web Platform](https://github.com/uky-web).  It covers launching the pre-configured [GitHub Codespaces](https://docs.github.com/en/codespaces) / [DDEV](https://ddev.readthedocs.io/en/stable/) environment and navigating common development workflows.

If you need assistance with your repo or using our development setup you can request a [**Web Platform Coder Lab**](https://web.uky.edu/connect).

**Quick Links:**

* [Launching Your Development Environment](#launching-your-development-environment)

* [Git: Branching and Committing](#git-branching-and-committing)

* [DDEV & Drush: Essential Commands](#ddev--drush-essential-commands)

* [Configuration Management](#configuration-management)

* [Dependency Management (Composer)](#dependency-management-composer)

* [Custom Themes and Modules](#custom-themes-and-modules)

* [Custom GitHub Actions](#custom-github-actions)

* [Running Locally (Alternative to Codespaces)](#running-locally-alternative-to-codespaces)

* [Training and Support](#training-and-support)

---

## Launching Your Development Environment

1.  **Create a new feature branch in GitHub:** Navigate to your project on GitHub.  Click the **"Branch"** dropdown which defaults to env-webcom, then in the **"Find or create branch..."** search box you can type a new branch name (e.g., `feature/my-new-feature`, `feature/design-updates`, `bug-fix/issue-123`) and create it from `env-webcom`.  It will automatically switch you to the newly created branch.

2.  **Launch Codespace from your new branch:** Once your branch is created, ensure it's selected in the branch dropdown.  Then, click the **"Code"** dropdown, select the **"Codespaces"** tab, and click **"Create codespace on [your-feature-branch-name]"**.  This ensures your development environment starts with your new branch checked out.

3.  **Wait for Automation**:  Once VS Code opens in your browser you will see a pop up box in the bottom right corner that says "Setting up remote connection: Building codespace..."  If you click the link you can watch the build process.  A `postCreateCommand` script will run automatically.  This process can take 5-10 minutes and will:

    * Start the DDEV environment.

    * Install all Composer dependencies.

    * **Import a recent, sanitized database** from the live environment.

    * **Enable Stage File Proxy**, which hotlinks images and files directly from the live site.  This means you don't need a local copy of the files directory.

    * Create a default `admin` user.

4.  **Access the Site**:  Once the setup script finishes, go to the **Ports** tab in VS Code and click the "Open in Browser" icon (globe) for Port 80.  The site will open in a new tab, fully installed and ready for development.  Using your linkblue username and password will **not** work in the ddev environment but you can login to the site with the default `admin` user.

    * **Username:** `admin`

    * **Password:** `admin`

**Note:** New content or content changes made within Codespaces development environment are **not** tracked in the repository.  This environment is for code and configuration development, not content staging.  All content updates should be performed directly on the live site.

**Important:** To conserve Codespace resource limits, when you are finished working on a Codespace (e.g., after a Pull Request is merged), remember to delete it.  Deleting a Codespace removes it completely, and **any uncommitted changes within that Codespace will be lost.** You can do this by clicking the **"Code"** dropdown, selecting the **"Codespaces"** tab, and then clicking the three dots (`...`) next to the Codespace you wish to delete and selecting **"Delete"**.

---

## Git: Branching and Committing

The primary branch is `env-webcom`, and it is protected.  All work must be done in a feature branch and submitted as a Pull Request.

**IMPORTANT:** Automated scripts push configuration changes to `env-webcom` daily.  There is a daily config export at 4 am and then Thursday evenings a sanitized database dump and config export.

1.  **Make your code changes.** Aim for small, focused changes and try to submit Pull Requests before the end of the day to minimize potential merge conflicts.

2.  **Stage and commit your changes:**

    ```bash
    # Add all changes to the staging area
    git add .

    # Commit the changes with a clear message
    git commit -m "feat: Describe the new feature or fix"
    ```

3.  **Push your branch to the remote repository:**

    ```bash
    git push -u origin feature/my-new-feature
    ```

4.  **For long-lived branches or to incorporate recent `env-webcom` changes:**
    If `env-webcom` has been updated since you created your branch, you should rebase your feature branch to incorporate those changes cleanly.

    ```bash
    git checkout env-webcom
    git pull # Get the latest changes from env-webcom
    git checkout feature/my-new-feature
    git rebase env-webcom # Rebase your feature branch onto the latest env-webcom
    git push --force-with-lease # Force push your rebased branch (use with caution)
    ```

5.  **Create a Pull Request** in the GitHub interface to have your changes reviewed and merged into the `env-webcom` branch.  **Note:** Once a Pull Request is processed and merged, the feature branch **will be deleted**.

---

## DDEV & Drush: Essential Commands

All Drupal (`drush`) and `composer` commands should be prefixed with `ddev` to ensure they run inside the correct container.

| Command | Description |
| :------ | :---------- |
| `ddev drush cr` | **Cache Rebuild.** Clears all of Drupal's caches.  Run this after making code changes or whenever the site behaves unexpectedly. |
| `ddev drush updb -y` | **Update Database.** Applies any pending database updates from module or core updates.  Run this after pulling new code or running `composer update`. |
| `ddev drush ws` | **Watchdog Show.** Displays recent log messages from Drupal's watchdog, useful for debugging errors and system events. |
| `ddev drush en [module_name] -y` | **Enable Module.** Enables one or more modules on your Drupal site, faster than using the UI. |
| `ddev drush pm:uninstall [module_name] -y` | **Uninstall Module.** Uninstalls one or more modules from your Drupal site. |
| `ddev drush status` | **Site Status.** Provides a quick overview of your Drupal site's health and environment. |

---

## Configuration Management

Configuration changes (e.g., creating fields, views, enabling modules) are stored in YAML files in the `config/sync` directory and tracked in Git.

**Automated Configuration Export:** A script automatically runs `drush cex` every morning at 4 AM and commits the changes to the `env-webcom` branch.  This makes it critical to always pull the latest changes before starting work.

| Command | Description |
| :------ | :---------- |
| `ddev drush cex` | **Configuration Export.** Exports all configuration from the database into the `config/sync` directory.  **Run** this after you make any configuration changes in the **Drupal UI.** The resulting file changes should be committed to Git. |
| `ddev drush cim` | **Configuration Import.** Imports configuration from the `config/sync` directory into the database.  This overwrites the database configuration.  **Run this after pulling code that contains configuration updates.** |

**Typical Config Workflow:**

1.  Pull the latest code from `env-webcom` (or rebase your branch as described above).

2.  Apply database and config changes from the repository: `ddev drush updb -y` and then `ddev drush cim -y`.

3.  Make your changes in the UI.

4.  Export your changes to code: `ddev drush cex`.

5.  Commit the resulting changes in the `config/sync` directory and push your branch.

---

## Dependency Management (Composer)

PHP dependencies, including Drupal modules and themes, are managed with Composer.  You can extend the site's functionality by adding contributed modules from [Drupal.org](https://drupal.org) to enhance features and integrations.  

| Command | Description |
| :------ | :---------- |
| `ddev composer require drupal/module_name` | Downloads and installs a new module.  Example: `ddev composer require 'drupal/admin_toolbar:^3.4'` |
| `ddev composer update` | Updates all dependencies to their latest allowed versions based on `composer.json`. |
| `ddev composer remove drupal/module_name` | Removes a module from composer and codebase. |

**Important:** Never manually edit the contents of `web/modules/contrib`, `web/themes/contrib`, or the `vendor` directory.  After running a composer command that changes `composer.json` or `composer.lock`, commit those files to your repository.  

While the Web Platform team supports the default platform and code provided within this repository, any extended or custom code beyond this is the developer's responsibility to maintain.

**Proper Removal Workflow:** When removing a module or theme, **do not delete it from `composer.json` first**. Doing so will cause issues with configuration imports/exports. Follow these steps to properly remove a module or theme:

1.  **Uninstall the module or theme:** Run `ddev drush pm:uninstall [module_name]`
2.  **Export the updated configuration:** Run `ddev drush cex`
3.  **Submit a pull request (PR):** Commit the config changes and submit a **dedicated PR** for the uninstall. This must be reviewed and merged before proceeding to Composer removal.
4.  **Remove from Composer:** Once the first PR is merged, run `ddev composer remove drupal/[module_name]`
5.  **Submit a second pull request:** Commit the `composer.json` and `composer.lock` changes in a **second PR**.

> ⚠️ Skipping any of these steps will result in broken configuration and failed config imports. If you're unsure about this process, please request a [Web Platform Coder Lab](https://web.uky.edu/connect) for assistance.

---

## Custom Themes and Modules

A [sub theme boilerplate](https://github.com/uky-web/uky_subtheme_boilerplate) is available as a starting point for adding custom templates and styling.  A sub-theme inherits all the resources of its base theme, allowing you to customize the site's appearance and functionality without directly modifying core or contributed themes, which simplifies updates and maintenance.

For custom themes, place them in `web/themes/custom/`.  Custom modules should be placed in `web/modules/custom/`.  If the either custom directory doesn't exist you will need to create it.

---

## Custom GitHub Actions

This repository has custom GitHub Actions that can be run on-demand to perform tasks on the live site (env-webcom).  You can access the GitHub Actions by clicking on the Actions tab in the GitHub UI.  These actions must be triggered from the `env-webcom` branch.

* **Developer - Config Export from env-webcom**:  Manually triggers a configuration export on the webcom hosted site.  Then commits the results to the env-webcom branch.
    * **Scenario:** This is useful if you've made configuration changes directly on the live site.  For example, if you made config changes on the live site after the 4 AM automated config export, then created a new branch, made further config changes in Codespaces, and submitted a Pull Request, your live site changes could be lost when `drush cim` is run.  Running this action ensures your repository reflects the latest live configuration before you develop.

* **Developer - Sanitized database and Config Export from env-webcom**:  Manually triggers a sanitized database dump and configuration export on the webcom hosted site.  Then commits the results to the env-webcom branch.
    * **Scenario:** Use this when you need to pull in the most recent content from the live site to develop against, especially if significant content updates have occurred since the Thursday evening automated database export.

---

## Running Locally (Alternative to Codespaces)

If you prefer to work on your local machine, you can set up the project using DDEV.

### Prerequisites

* **Containerization Software**
    * **[Docker Desktop](https://www.docker.com/products/docker-desktop/)**:  A widely used containerization platform available for Mac, Windows, and Linux.  Ensure it is installed and running.
    * **[OrbStack](https://orbstack.dev/)**: (Recommended for Mac) A faster, lighter, and more powerful alternative to Docker Desktop specifically for macOS.  Ensure it is installed and running.

* **DDEV Installation**
    * **DDEV:** Install DDEV (refer to the [DDEV documentation](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/) for your operating system).

### Setup Steps

1.  **Clone the repository:**
    ```bash
    git clone [your-repository-url]
    cd [your-repository-name]
    ```

2.  **Start DDEV:**
    ```bash
    ddev start
    ```
    This will pull the necessary Docker images and start the web and database containers.

3.  **Install Composer dependencies:**
    ```bash
    ddev composer install
    ```

4.  **Run the site installation script:**
    ```bash
    ./.ddev/uk-site-install.sh
    ```
    This script will handle importing the database, enabling Stage File Proxy, and setting up the default admin user.

5.  **Access the Site:**
    Once DDEV starts, it will provide a local URL (e.g., `https://[project-name].ddev.site`).  Open this URL in your browser.  Using your linkblue username and password will **not** work in the ddev environment but you can login to the site with the default `admin` user.

    * **Username:** `admin`
    * **Password:** `admin`

---

## Training and Support

The [UK Marketing and Brand Strategy Web Communications](https://web.uky.edu/) team develops, hosts, and maintains the University of Kentucky Web Platform.  The platform provides a range of features and designs consistent with the university brand guidelines and web best practices.  They offer various training and support resources to assist you.

### Training Resources

* [**Web Platform Training**](https://web.uky.edu/training)
* [**Content Strategy**](https://web.uky.edu/content-strategy)

### Web Platform Lab Sessions

* [**Web Platform Lab**](https://web.uky.edu/connect): The Web Platform Lab is a 1 hour walkthrough of content creation and management on the UK Web Platform.
* [**Web Platform Coder Lab**](https://web.uky.edu/connect): The Web Platform Coder Lab offers training and support for code access, development environment setup, and general questions related to developing extended Web Platform sites.

### Support Forms

* [**General Support**](https://web.uky.edu/form/questions): UK Web Comm. general support form
* [**Site Request**](https://web.uky.edu/form/site-request-form): Request a new UK Web Platform site
* [**Go Live**](https://web.uky.edu/form/go-live): Request to move from development to production
* [**Site Removal Request**](https://web.uky.edu/form/site-removal-form): Request to remove your site.
* [**Add, Update, Remove Users**](https://web.uky.edu/form/add-user-to-site): Request new roles for users on your UK Web Platform site

### University Web Policy and Brand Guidelines

* [**University Web Policy**](https://web.uky.edu/university-web-policy)
* [**Brand Guidelines**](https://brand.uky.edu)
