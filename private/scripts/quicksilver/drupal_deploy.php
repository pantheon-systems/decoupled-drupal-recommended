<?
//Run schema updates
echo "Running Drupal updates.\n";
passthru('drush updb -y');
echo "Updating drupal complete.\n";
// Import all config changes.
echo "Importing configuration from yml files...\n";
passthru('drush config-import -y');
echo "Import of configuration complete.\n";
//Clear all cache
echo "Rebuilding cache.\n";
passthru('drush cr');
echo "Rebuilding cache complete.\n";
