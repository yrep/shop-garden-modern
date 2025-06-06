<?php
if (!defined('ABSPATH')) {
    exit;
}

class GmsWcAjax {

    public function __construct() {

        add_action('wp_ajax_gms_clear_cart', [$this, 'clear_cart']);
        add_action('wp_ajax_nopriv_gms_clear_cart', [$this, 'clear_cart']);

        add_action('wp_ajax_gms_remove_from_cart', [$this, 'remove_from_cart']);
        add_action('wp_ajax_nopriv_gms_remove_from_cart', [$this, 'remove_from_cart']);

        add_action('wp_ajax_gms_update_cart_item_quantity', [$this, 'update_cart_item_quantity']);
        add_action('wp_ajax_nopriv_gms_update_cart_item_quantity', [$this, 'update_cart_item_quantity']);

        add_action('wp_ajax_gms_get_cart_checkout_content', [$this, 'get_cart_checkout_content']);
        add_action('wp_ajax_nopriv_gms_get_cart_checkout_content', [$this, 'get_cart_checkout_content']);

        add_action('wp_ajax_gms_update_cart_quantity', [$this, 'update_cart_quantity']);
        add_action('wp_ajax_nopriv_gms_update_cart_quantity', [$this, 'update_cart_quantity']);

        add_action('wp_ajax_gms_update_checkout_totals', [$this, 'update_checkout_totals']);
        add_action('wp_ajax_nopriv_gms_update_checkout_totals', [$this, 'update_checkout_totals']);
    
    }

    public function clear_cart() {
        if (!class_exists('WC_Cart')) {
            wp_send_json_error(['message' => 'WooCommerce is not active']);
        }

        $cart = WC()->cart;
        $cart->empty_cart();
        wp_send_json_success(['message' => 'Cart cleared']);
    }

    // Remove item from cart handler
    public function remove_from_cart() {
        if (!class_exists('WC_Cart')) {
            wp_send_json_error(['message' => 'WooCommerce is not active']);
        }

        $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';

        if (empty($cart_item_key)) {
            wp_send_json_error(['message' => 'Invalid cart item key']);
        }
        $cart = WC()->cart;
        $cart->remove_cart_item($cart_item_key);

        $this->responseToFront();
    }


    public function update_cart_quantity() {
        if (!class_exists('WC_Cart')) {
            wp_send_json_error(['message' => 'WooCommerce is not active']);
        }
    
        $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';
        $new_quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    
        if (empty($cart_item_key) || $new_quantity < 1) {
            wp_send_json_error(['message' => 'Invalid cart item or quantity']);
        }
    
        $cart = WC()->cart;
    
        $cart_item = $cart->get_cart_item($cart_item_key);
    
        if (!$cart_item) {
            wp_send_json_error(['message' => 'Cart item not found']);
        }
    
        $product = $cart_item['data'];
        $current_quantity = $cart_item['quantity'];
        
        if (!$product->is_purchasable()) {
            wp_send_json_error(['message' => 'Product is not available for purchase']);
        }
    
        if ($product->managing_stock() && !$product->is_on_backorder($new_quantity)) {
            $stock_quantity = $product->get_stock_quantity();
    
            if ($stock_quantity < $new_quantity) {
                wp_send_json_error([
                    'message' => 'Не получилось увеличить количество товара ' . $product->get_name() . ' в заказе до ' . $new_quantity . ' шт. Остаток на складе: ' . $stock_quantity . ' шт.' . PHP_EOL . 'Свяжитесь с нами, чтобы уточнить сроки поступления необходимого количества товара.',
                ]);
            }
        }
    
        $cart->set_quantity($cart_item_key, $new_quantity, true);
        $this->responseToFront($new_quantity);
    }
    
    
    public function responseToFront($quantity = null){
        $cart_data = WC()->cart->get_cart();

        if (!WC()->cart->is_empty()) {
            $subtotal = WC()->cart->get_cart_subtotal();
            $total = WC()->cart->get_total();
            $taxes = WC()->cart->get_taxes_total();
            $shipping = WC()->cart->get_cart_shipping_total();
            $count = WC()->cart->get_cart_contents_count();
            $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();

            foreach ($cart_data as $cart_item_key => $cart_item) {
                $items_with_tax[$cart_item_key] = [
                    'product_id' => $cart_item['product_id'],
                    'quantity' => $cart_item['quantity'],
                    'price_per_item' => round(wc_get_price_including_tax($cart_item['data']), 0),
                    'line_total' => round($cart_item['line_total'] + $cart_item['line_tax'], 0),
                    'formatted_total' => wc_price(round($cart_item['line_total'] + $cart_item['line_tax'], 0))
                ];
            }
        }

        // Data with new quantity
        wp_send_json_success([
            'new_quantity' => $quantity,
            'cart_data' => $cart_data,
            'cart_data_with_tax' => $items_with_tax,
            'totals' => [
                'subtotal' => $subtotal,
                'total' => $total,
                'taxes' => $taxes,
                'shipping' => $shipping,
                'count' => $count,
            ],
            'payment_gateways' => $payment_gateways,
        ]);
    }


    public function get_cart_checkout_content() {
        if (!class_exists('WooCommerce')) {
            wp_send_json_error(['message' => 'WooCommerce is not active']);
        }

        WC()->cart->calculate_totals();

        $cart = WC()->cart->get_cart();

        ob_start();

        include(plugin_dir_path(__FILE__) . '../templates/modal_checkout.php'); // Modal window template
        ?>
        <div class="cart-container">
        <?php
        if (!empty($cart)) {
            foreach ($cart as $cart_item_key => $cart_item) {

                $product = $cart_item['data'];
                $product_id = $cart_item['product_id'];
                $quantity = $cart_item['quantity'];
                $product_name = $product->get_name();
                $product_price = $product->get_price();
                $product_thumbnail = get_the_post_thumbnail($product_id, 'thumbnail');
                $product_total = $product_price * $quantity;

                ?>
                <div class="cart-item" data-cart_item_key="<?php echo $cart_item_key; ?>" data-product_id="<?php echo $product_id; ?>">
                    <div class="cart-item-info-container">
                        <div class="cart-item-image">
                            <?php echo $product_thumbnail; ?>
                        </div>
                        <div class="cart-item-description">
                            <p><?php echo esc_html($product_name); ?></p>
                        </div>
                    </div>
                    <div class="cart-item-quantity-price-container">
                        <div class="cart-item-price">
                            <p>Цена: <?php echo wc_price($product_price); ?></p>
                        </div>
                        <div class="cart-item-quantity-wrapper">
                            <button class="gms-decrease-quantity btn-left icon-button">
                                <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                >
                                <path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10 -10 10a10 10 0 1 1 0 -20m-2.293 8.293a1 1 0 0 0 -1.414 1.414l3 3a1 1 0 0 0 1.414 0l3 -3a1 1 0 0 0 0 -1.414l-.094 -.083a1 1 0 0 0 -1.32 .083l-2.294 2.292z" />
                                </svg>
                            </button>
                            <input type="number" class="gms-item-quantity" value="<?php echo esc_attr($quantity); ?>" min="1" />
                            <button class="gms-increase-quantity btn-center icon-button">
                                <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                >
                                <path d="M17 3.34a10 10 0 1 1 -15 8.66l.005 -.324a10 10 0 0 1 14.995 -8.336m-4.293 5.953a1 1 0 0 0 -1.414 0l-3 3a1 1 0 0 0 0 1.414l.094 .083a1 1 0 0 0 1.32 -.083l2.293 -2.292l2.293 2.292a1 1 0 0 0 1.414 -1.414z" />
                                </svg>
                            </button>
                        </div>
                        <div class="gms-item-total-container">
                            <p class="gms-item-total"><?php echo $product_total; ?>&nbsp₽</p>
                        </div>
                        <div class="gms-remove-item-container">
                        <button class="gms-remove-item btn-right icon-button">
                            <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            >
                            <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-6.489 5.8a1 1 0 0 0 -1.218 1.567l1.292 1.293l-1.292 1.293l-.083 .094a1 1 0 0 0 1.497 1.32l1.293 -1.292l1.293 1.292l.094 .083a1 1 0 0 0 1.32 -1.497l-1.292 -1.293l1.292 -1.293l.083 -.094a1 1 0 0 0 -1.497 -1.32l-1.293 1.292l-1.293 -1.292l-.094 -.083z" />
                            </svg>
                        </button>
                    </div>
                    </div>
                    
                </div>
                <div class="cart-items-divider"></div>
                
                <?php
            }
            ?>
            </div>
            <?php
            echo '<div id="cart-error-message" class="error"></div>';
            echo '<div class="cart-totals">';
            echo '<span>Всего товаров <span id="checkout-items-count"><b>&nbsp' . WC()->cart->get_cart_contents_count() . ' </b></span></span>';
            echo '<span>&nbsp на общую сумму <span id="checkout-total"><b>&nbsp' . WC()->cart->get_total() . ' </b></span></span>';
            echo '</div>';
            echo '<p><b>Стоимость доставки не входит в сумму заказа.</b></p>';
            echo '<p>Найти ближайший к вам пункт <a target="_blank" href="https://www.cdek.ru/ru/offices/">СДЭК</a>.</p>';
            echo '<p>Укажите код пункта или его адрес в примечании к заказу (ниже в форме "Детали").</p>';

        } else {
            ?>
        </div>
        <?php
            echo '<p>Ваша корзина пуста.</p>';
        }



        // New order form
        echo '<div class="woocommerce-checkout-form">';
        echo do_shortcode('[woocommerce_checkout]'); // Order form shortcode
        echo '</div>';

        $content = ob_get_clean();
        wp_send_json_success(['content' => $content]);
    }

    public function update_checkout_totals() {
        if (!class_exists('WooCommerce')) {
            wp_send_json_error(['message' => 'WooCommerce is not active']);
        }

        WC()->cart->calculate_totals();
        
        $cart_total = WC()->cart->get_cart_total();
        $cart_subtotal = WC()->cart->get_cart_subtotal();
        $cart_contents_count = WC()->cart->get_cart_contents_count();
        $shipping_total = WC()->cart->get_shipping_total();
        $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();

        $total = WC()->cart->get_total();
        $discount_total = WC()->cart->get_discount_total();

        wp_send_json_success([
            'cart_total' => $cart_total,
            'cart_subtotal' => $cart_subtotal,
            'cart_contents_count' => $cart_contents_count,
            'shipping_total' => $shipping_total,
            'total' => $total,
            'discount_total' => $discount_total,
            'payment_gateways' => $payment_gateways,
        ]);
    }
}
