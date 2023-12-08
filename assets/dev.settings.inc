# settings.lando.php include.
if (getenv('DRUPAL_ENV') == 'lando') {
   include $app_root . '/' . $site_path . '/settings.lando.php';
 }

# settings.ddev.php include.
 if (file_exists(__DIR__ . '/settings.ddev.php') && getenv('IS_DDEV_PROJECT') == 'true') {
  include __DIR__ . '/settings.ddev.php';
}
