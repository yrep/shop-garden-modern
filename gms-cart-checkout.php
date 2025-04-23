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

define('GMS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GMS_PLUGIN_URL', plugin_dir_url(__FILE__));

class GmsCartCheckout {

    public function __construct() {

        if (!$this->isWoocommerceActive()) {
            add_action('admin_notices', [$this, 'showWoocommerceMissingNotice']);
            return;
        }

        $this->load_dependencies();
        $this->init_classes();
        
        add_filter( 'woocommerce_min_password_strength', function() { return 0; } );
    }

    /**
     *  Checks WooCommerce
     */
    private function isWoocommerceActive() {
        return class_exists('WooCommerce') || 
        in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
    }

    /**
     *  Warning WooCommerce is not active
     */
    public function showWoocommerceMissingNotice() {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p>Для работы плагина GM Shop Cart and Checkout необходимо активировать WooCommerce.</p>';
        echo '</div>';
    }

    private function load_dependencies() {
        //include_once GMS_PLUGIN_DIR . 'helpers/GmsLogger.php';
        include_once GMS_PLUGIN_DIR . 'include/GmsAdminPage.php';
        include_once GMS_PLUGIN_DIR . 'include/GmsCommon.php';
        include_once GMS_PLUGIN_DIR . 'include/GmsWcAjax.php';
    }

    private function init_classes() {
        //new GmsLogger();
        new GmsAdminPage();
        new GmsCommon();
        new GmsWcAjax();
    }

    public static function activate() {
        //
    }

    public static function deactivate() {
        //
    }

    public static function uninstall() {
        //
    }
}

if (class_exists('GmsCartCheckout')) {
    register_activation_hook(__FILE__, ['GmsCartCheckout', 'activate']);
    register_deactivation_hook(__FILE__, ['GmsCartCheckout', 'deactivate']);
    register_uninstall_hook(__FILE__, ['GmsCartCheckout', 'uninstall']);

    new GmsCartCheckout();

    //___('Plugin init');
    //add_action('init', 'gms_log_incoming_request', 1);

}

/* For debug
function gms_log_incoming_request() {

    $request_uri = $_SERVER['REQUEST_URI'];

    if (strpos($request_uri, 'gms.log') !== false) {
        return;
    }

    if (strpos($request_uri, 'wp-cron.php') !== false) {
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
*/