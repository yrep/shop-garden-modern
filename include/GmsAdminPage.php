<?php
if (!defined('ABSPATH')) {
    exit;
}

class GmsAdminPage {

    public function __construct() {
        //add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }
    /*
    public function add_admin_menu() {
        add_menu_page(
            'GMS Cart Checkout Settings',
            'GMS Settings',
            'manage_options',
            'gms-cart-checkout-settings',
            [$this, 'render_settings_page'],
            'dashicons-cart',
            100
        );
    }
*/
    public function register_settings() {
        register_setting('gms_settings_group', 'gms_modal_window_name');
        add_settings_section('gms_main_section', 'Основные настройки', null, 'gms-cart-checkout-settings');
        add_settings_field('gms_modal_window_name', 'Название модального окна', [$this, 'render_modal_name_field'], 'gms-cart-checkout-settings', 'gms_main_section');
    }

    public function render_modal_name_field() {
        $value = get_option('gms_modal_window_name', '');
        echo '<input type="text" name="gms_modal_window_name" value="' . esc_attr($value) . '" />';
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>GMS Cart Checkout Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('gms_settings_group');
                do_settings_sections('gms-cart-checkout-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}