<?php

/*
 * CURRENCY UPDATER - v 1.0.0 (22.04.2014)
*/

define('OPENCART_ROOT_DIR', '/home/mygoodhouse2/www/admin/');

require_once(OPENCART_ROOT_DIR . "config.php");

require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
$registry->set('db', $db);


// Store
if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
        $store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`ssl`, 'www.', '') = '" . $db->escape('https://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
} else {
        $store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`url`, 'www.', '') = '" . $db->escape('http://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
}

if ($store_query->num_rows) {
        $config->set('config_store_id', $store_query->row['store_id']);
} else {
        $config->set('config_store_id', 0);
}

// Settings
$query = $db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' OR store_id = '" . (int)$config->get('config_store_id') . "' ORDER BY store_id ASC");

foreach ($query->rows as $result) {
        if (!$result['serialized']) {
                $config->set($result['key'], $result['value']);
        } else {
                $config->set($result['key'], json_decode($result['value'], true));
        }
}

if (!$store_query->num_rows) {
        $config->set('config_url', HTTP_SERVER);
        $config->set('config_ssl', HTTPS_SERVER);
}

$cache = new Cache('file');
$registry->set('cache', $cache);
//Currency update
$loader->model('localisation/currency');
$model_catalog_category = $registry->get('model_localisation_currency');
$model_catalog_category->refresh(true);
echo "Done!\n";
?>