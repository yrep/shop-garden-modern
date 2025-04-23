<?php
if (!defined('ABSPATH')) {
    exit;
}

class GmsCommon {
	
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_footer', [$this, 'add_modal_window_to_footer']);
        add_filter('woocommerce_locate_template', [$this, 'gmscc_override_woocommerce_template'], 10, 3);
        add_action('kadence_render_mobile_header_column', [$this, 'add_custom_button_to_kadence_mobile_navigation'], 10, 2);
        add_action('kadence_render_header_column', [$this, 'add_custom_button_to_kadence_navigation'], 10, 2);
        add_action('woocommerce_product_query', [$this, 'gms_filter_products_by_category']);
        add_filter('woocommerce_checkout_fields', [$this, 'gms_custom_remove_checkout_fields']);
        add_filter('woocommerce_order_button_html', [$this, 'gms_custom_checkout_button_html'], 900 );
        add_filter('wc_add_to_cart_message_html',  [$this, 'return_empty_string']);
        add_filter('woocommerce_account_menu_items', [$this, 'gms_remove_my_account_tabs'], 999);
        add_action('woocommerce_review_order_before_submit', [$this, 'add_custom_div_before_checkout_button'], 10);
    }


    public function return_empty_string(){
		return '';
	}
	
    public function add_custom_div_before_checkout_button() {
        echo '	<div id="gms-total-bottom"><p>Итоговая сумма: <span id="gms-total-bottom-summ">' . 
        WC()->cart->get_total() . '</span></p></div>';
    }

    
    public function gms_filter_products_by_category($query) {
        $tax_query = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => array('sadovye-instrumenty'),
            ),
        );
        $query->set('tax_query', $tax_query);
    }

    public function enqueue_assets() {
        wp_enqueue_style('gms-styles', GMS_PLUGIN_URL . 'assets/css/styles.css');
        wp_enqueue_script('gms-scripts', GMS_PLUGIN_URL . 'assets/js/scripts.js', [], null, true);

        wp_localize_script('gms-scripts', 'ajaxurl', admin_url('admin-ajax.php'));
    }

    function add_custom_button_to_kadence_mobile_navigation($row, $side) {
        if ($side === 'right') {
        echo $this->returnCartIcon(); 
        }
    }
    
    //'kadence_render_header_column', $row, 'right'
    function add_custom_button_to_kadence_navigation($row, $side) {
        if ($side === 'right') {
        echo $this->returnCartIcon();
        }
    }

    private function returnCartIcon() {
        $cart_count = WC()->cart->get_cart_contents_count();
    
        $cart_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <path d="M16 10a4 4 0 0 1-8 0"></path>
        </svg>';
    
        return sprintf(

            '<div class="gms-header-cart-icon-container">
                <a href="javascript:void(0)" class="gms-cart-icon" onclick="gmsOpenCart()">
                    %s
                    <span class="cart-count header-cart-total">%d</span>
                </a>
            </div>',

            $cart_icon,
            $cart_count
        );
    }


    
    function gms_remove_my_account_tabs($items) {
        unset($items['downloads']);
        return $items;
    }


    public function add_modal_window_to_footer() {
        include plugin_dir_path(__FILE__) . '../template/modal_checkout.php';

    }
    

    public function gmscc_override_woocommerce_template($template, $template_name, $template_path) {

        $plugin_path = __DIR__ . '/../templates/woocommerce/';

        $custom_template = $plugin_path . $template_name;

        if (file_exists($custom_template)) {
            return $custom_template;
        }
        return $template;
    }

    function gms_custom_remove_checkout_fields($fields) {
        unset($fields['billing']['billing_company']);
        unset($fields['billing']['billing_address_1']);
        unset($fields['billing']['billing_address_2']);
        unset($fields['billing']['billing_city']);
        unset($fields['billing']['billing_postcode']);
        unset($fields['billing']['billing_country']);
        unset($fields['billing']['billing_state']);

        unset($fields['shipping']['shipping_first_name']);
        unset($fields['shipping']['shipping_last_name']);
        unset($fields['shipping']['shipping_company']);
        unset($fields['shipping']['shipping_address_1']);
        unset($fields['shipping']['shipping_address_2']);
        unset($fields['shipping']['shipping_city']);
        unset($fields['shipping']['shipping_postcode']);
        unset($fields['shipping']['shipping_country']);
        unset($fields['shipping']['shipping_state']);

        $fields['order']['order_comments']['required'] = true;
        $fields['order']['order_comments']['placeholder'] = 'Вставьте сюда код или адрес ближайшего к вам пункта СДЭК, и любые другие примечания, касающиеся доставки и получения заказа';
        
        return $fields;
    }

    function gms_custom_checkout_button_html( $button_html ) {
        $original_text  = __( 'Place order', 'woocommerce' );
        $custom_text    = 'Оплатить картой через Т-Банк';

        $button_html = str_replace( $original_text, $custom_text, $button_html );
        return $button_html;
    }
}