<?php
/*
 * Plugin Name: GM Shop Cart and Checkout
 * Description: Модальное окно для управления корзиной и формой оформления заказа.
 * Version: 0.3.5
 * Author: Alexnder Yurkinskiy
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GMS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GMS_PLUGIN_URL', plugin_dir_url(__FILE__));

class GmsCartCheckout {

    public function __construct() {
        $this->load_dependencies();
        $this->init_classes();
    }

    private function load_dependencies() {
        // foreach ( glob( GMS_PLUGIN_DIR . "includes/*.php" ) as $file ) {
        //     include_once $file;
        // }
        include_once GMS_PLUGIN_DIR . 'include/GmsAdminPage.php';
        include_once GMS_PLUGIN_DIR . 'include/GmsCommon.php';
        include_once GMS_PLUGIN_DIR . 'include/GmsWcAjax.php';

    }

    private function init_classes() {
        new GmsAdminPage();
        new GmsCommon();
        new GmsWcAjax();
    }

    public static function activate() {
        // Activation logic (if needed)
    }

    public static function deactivate() {
        // Deactivation logic (if needed)
    }

    public static function uninstall() {
        // Uninstall logic (if needed)
    }
}

// Initialize the plugin
if (class_exists('GmsCartCheckout')) {
    register_activation_hook(__FILE__, ['GmsCartCheckout', 'activate']);
    register_deactivation_hook(__FILE__, ['GmsCartCheckout', 'deactivate']);
    register_uninstall_hook(__FILE__, ['GmsCartCheckout', 'uninstall']);

    new GmsCartCheckout();
}
