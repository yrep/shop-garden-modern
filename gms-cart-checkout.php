<?php
/*
 * Plugin Name: GM Shop Cart and Checkout
 * Description: Модальное окно для управления корзиной и формой оформления заказа.
 * Version: 0.3.6
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
        include_once GMS_PLUGIN_DIR . 'helpers/GmsLogger.php';
        include_once GMS_PLUGIN_DIR . 'include/GmsAdminPage.php';
        include_once GMS_PLUGIN_DIR . 'include/GmsCommon.php';
        include_once GMS_PLUGIN_DIR . 'include/GmsWcAjax.php';
    }

    private function init_classes() {
        new GmsLogger();
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

if (class_exists('GmsCartCheckout')) {
    register_activation_hook(__FILE__, ['GmsCartCheckout', 'activate']);
    register_deactivation_hook(__FILE__, ['GmsCartCheckout', 'deactivate']);
    register_uninstall_hook(__FILE__, ['GmsCartCheckout', 'uninstall']);

    new GmsCartCheckout();
    ___('Plugin init');

    add_action('init', 'gms_log_incoming_request', 1);

}


function gms_log_incoming_request() {

    if (strpos($_SERVER['REQUEST_URI'], 'gms.log') !== false) {
        return;
    }

    $payload = file_get_contents('php://input');

    $log_data = [
        'method'       => $_SERVER['REQUEST_METHOD'] ?? '',
        'uri'          => $_SERVER['REQUEST_URI'] ?? '',
        '_GET'         => $_GET,
        '_POST'        => $_POST,
        '_REQUEST'     => $_REQUEST,
        //'_COOKIE'      => $_COOKIE,
        //'_FILES'       => $_FILES,
        //'_SERVER'      => $_SERVER,
        //'_SESSION'     => isset($_SESSION) ? $_SESSION : [],
        'php://input'  => $payload,
    ];

    ___($log_data, 'HTTP Request');
}